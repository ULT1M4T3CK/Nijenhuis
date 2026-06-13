#!/usr/bin/env python3
"""
Conversation Context Manager
Maintains conversation history and context for improved chatbot responses
"""

import json
import os
import time
import uuid
import re
from typing import Dict, List, Optional, Tuple, Any
from datetime import datetime, timedelta
from collections import deque

class ConversationContext:
    """Manages conversation history and context for a single session"""
    
    def __init__(self, session_id: str, max_history: int = 20):
        """
        Initialize conversation context
        
        Args:
            session_id: Unique session identifier
            max_history: Maximum number of messages to keep in history
        """
        self.session_id = session_id
        self.max_history = max_history
        self.messages: deque = deque(maxlen=max_history)
        self.created_at = datetime.now()
        self.last_activity = datetime.now()
        self.metadata: Dict[str, Any] = {}
        
    def add_message(self, role: str, content: str, metadata: Dict[str, Any] = None):
        """
        Add a message to the conversation history
        
        Args:
            role: 'user' or 'assistant'
            content: Message content
            metadata: Optional metadata (timestamp, confidence, etc.)
        """
        message = {
            'role': role,
            'content': content,
            'timestamp': datetime.now().isoformat(),
            'metadata': metadata or {}
        }
        self.messages.append(message)
        self.last_activity = datetime.now()
        
    def get_conversation_history(self, max_tokens: Optional[int] = None) -> List[Dict[str, str]]:
        """
        Get conversation history in a format suitable for models
        
        Args:
            max_tokens: Optional limit on total tokens (approximate)
            
        Returns:
            List of messages with 'role' and 'content' keys
        """
        history = []
        token_count = 0
        
        for msg in self.messages:
            content = msg['content']
            # Approximate token count (1 token ≈ 4 characters)
            msg_tokens = len(content) // 4
            
            if max_tokens and token_count + msg_tokens > max_tokens:
                break
                
            history.append({
                'role': msg['role'],
                'content': content
            })
            token_count += msg_tokens
            
        return history
    
    def get_full_context(self) -> str:
        """
        Get full conversation context as a formatted string
        
        Returns:
            Formatted conversation history
        """
        context_parts = []
        for msg in self.messages:
            role_label = "User" if msg['role'] == 'user' else "Assistant"
            context_parts.append(f"{role_label}: {msg['content']}")
        return "\n".join(context_parts)
    
    def get_recent_context(self, n_messages: int = 5) -> List[Dict[str, str]]:
        """
        Get recent N messages for context
        
        Args:
            n_messages: Number of recent messages to return
            
        Returns:
            List of recent messages
        """
        return list(self.messages)[-n_messages:]
    
    def clear_history(self):
        """Clear conversation history"""
        self.messages.clear()
    
    def to_dict(self) -> Dict[str, Any]:
        """Convert context to dictionary for serialization"""
        return {
            'session_id': self.session_id,
            'messages': list(self.messages),
            'created_at': self.created_at.isoformat(),
            'last_activity': self.last_activity.isoformat(),
            'metadata': self.metadata
        }
    
    @classmethod
    def from_dict(cls, data: Dict[str, Any]) -> 'ConversationContext':
        """Create context from dictionary"""
        context = cls(data['session_id'])
        context.messages = deque(data.get('messages', []), maxlen=context.max_history)
        context.created_at = datetime.fromisoformat(data['created_at'])
        context.last_activity = datetime.fromisoformat(data['last_activity'])
        context.metadata = data.get('metadata', {})
        return context


class ConversationContextManager:
    """Manages multiple conversation contexts (sessions)"""
    
    def __init__(self, storage_dir: Optional[str] = None, session_timeout: int = 3600):
        """
        Initialize context manager
        
        Args:
            storage_dir: Directory to persist conversation contexts
            session_timeout: Session timeout in seconds (default 1 hour)
        """
        self.contexts: Dict[str, ConversationContext] = {}
        self.storage_dir = storage_dir
        self.session_timeout = session_timeout
        
        if storage_dir:
            os.makedirs(storage_dir, exist_ok=True)
            self._load_persisted_contexts()
    
    def _validate_session_id(self, session_id: str) -> bool:
        """
        Validate session ID format to prevent path traversal attacks
        
        Args:
            session_id: Session ID to validate
            
        Returns:
            True if valid, False otherwise
        """
        if not isinstance(session_id, str):
            return False
        
        # Check for path traversal attempts
        if '..' in session_id or '/' in session_id or '\\' in session_id:
            return False
        
        # Allow UUID format (with or without 'session_' prefix) or alphanumeric with underscores and hyphens
        # Pattern: session_<alphanumeric>_<alphanumeric> or UUID format
        uuid_pattern = r'^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$'
        session_pattern = r'^session_[a-zA-Z0-9_-]+$'
        
        # Remove 'session_' prefix for UUID check
        id_for_check = session_id.replace('session_', '', 1) if session_id.startswith('session_') else session_id
        
        # Check if it's a valid UUID or matches session pattern
        if re.match(uuid_pattern, id_for_check) or re.match(session_pattern, session_id):
            return True
        
        # Fallback: alphanumeric with underscores, hyphens, and dots (but no path separators)
        safe_pattern = r'^[a-zA-Z0-9_.-]+$'
        return bool(re.match(safe_pattern, session_id)) and len(session_id) <= 128
    
    def get_or_create_context(self, session_id: Optional[str] = None) -> ConversationContext:
        """
        Get existing context or create a new one
        
        Args:
            session_id: Optional session ID. If None, creates a new session
            
        Returns:
            ConversationContext instance
        """
        # Validate session_id if provided
        if session_id and not self._validate_session_id(session_id):
            # Invalid session ID, create new one
            session_id = None
        
        if session_id and session_id in self.contexts:
            context = self.contexts[session_id]
            # Check if session expired
            if (datetime.now() - context.last_activity).total_seconds() > self.session_timeout:
                # Session expired, create new one
                del self.contexts[session_id]
                session_id = None
        
        if not session_id:
            session_id = 'session_' + str(uuid.uuid4())
        
        if session_id not in self.contexts:
            self.contexts[session_id] = ConversationContext(session_id)
            if self.storage_dir:
                self._save_context(session_id)
        
        return self.contexts[session_id]
    
    def get_context(self, session_id: str) -> Optional[ConversationContext]:
        """Get context by session ID"""
        # Validate session ID before lookup
        if not self._validate_session_id(session_id):
            return None
        return self.contexts.get(session_id)
    
    def add_message(self, session_id: str, role: str, content: str, metadata: Dict[str, Any] = None):
        """Add message to a conversation context"""
        context = self.get_or_create_context(session_id)
        context.add_message(role, content, metadata)
        if self.storage_dir:
            self._save_context(session_id)
    
    def clear_context(self, session_id: str):
        """Clear a conversation context"""
        if session_id in self.contexts:
            del self.contexts[session_id]
            if self.storage_dir:
                context_file = os.path.join(self.storage_dir, f"{session_id}.json")
                if os.path.exists(context_file):
                    os.remove(context_file)
    
    def cleanup_expired_sessions(self):
        """Remove expired sessions"""
        now = datetime.now()
        expired_sessions = []
        
        for session_id, context in self.contexts.items():
            if (now - context.last_activity).total_seconds() > self.session_timeout:
                expired_sessions.append(session_id)
        
        for session_id in expired_sessions:
            self.clear_context(session_id)
        
        return len(expired_sessions)
    
    def _save_context(self, session_id: str):
        """Save context to disk"""
        # Validate session ID before saving
        if not self._validate_session_id(session_id) or session_id not in self.contexts:
            return
        
        try:
            # Use os.path.join and validate the final path is within storage_dir
            context_file = os.path.join(self.storage_dir, f"{session_id}.json")
            
            # Additional security: ensure the resolved path is within storage_dir
            resolved_path = os.path.abspath(context_file)
            storage_path = os.path.abspath(self.storage_dir)
            if not resolved_path.startswith(storage_path):
                print(f"⚠️ Security: Attempted path traversal detected for session {session_id}")
                return
            
            with open(context_file, 'w', encoding='utf-8') as f:
                json.dump(self.contexts[session_id].to_dict(), f, indent=2, ensure_ascii=False)
        except Exception as e:
            print(f"⚠️ Could not save context for session {session_id}: {e}")
    
    def _load_persisted_contexts(self):
        """Load persisted contexts from disk"""
        if not self.storage_dir or not os.path.exists(self.storage_dir):
            return
        
        try:
            for filename in os.listdir(self.storage_dir):
                if filename.endswith('.json'):
                    session_id = filename[:-5]  # Remove .json extension
                    context_file = os.path.join(self.storage_dir, filename)
                    
                    try:
                        with open(context_file, 'r', encoding='utf-8') as f:
                            data = json.load(f)
                            context = ConversationContext.from_dict(data)
                            
                            # Check if expired
                            if (datetime.now() - context.last_activity).total_seconds() <= self.session_timeout:
                                self.contexts[session_id] = context
                            else:
                                # Remove expired context file
                                os.remove(context_file)
                    except Exception as e:
                        print(f"⚠️ Could not load context from {context_file}: {e}")
        except Exception as e:
            print(f"⚠️ Could not load persisted contexts: {e}")
    
    def get_statistics(self) -> Dict[str, Any]:
        """Get statistics about managed contexts"""
        return {
            'total_sessions': len(self.contexts),
            'active_sessions': sum(
                1 for ctx in self.contexts.values()
                if (datetime.now() - ctx.last_activity).total_seconds() < 300  # Active in last 5 min
            ),
            'total_messages': sum(len(ctx.messages) for ctx in self.contexts.values())
        }

