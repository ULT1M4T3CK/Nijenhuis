import React, { useState, useRef, useEffect } from 'react';
import { 
  Send, 
  ThumbsUp, 
  ThumbsDown, 
  Edit3, 
  Save, 
  X, 
  RefreshCw,
  Loader2,
  CheckCircle2,
  AlertCircle,
  Sparkles,
  Bot,
  User
} from 'lucide-react';
import axios from 'axios';

function TrainingPage() {
  const [query, setQuery] = useState('');
  const [messages, setMessages] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [editingMessageId, setEditingMessageId] = useState(null);
  const [editedResponse, setEditedResponse] = useState('');
  const [saveStatus, setSaveStatus] = useState(null);
  const messagesEndRef = useRef(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!query.trim() || isLoading) return;

    const userMessage = {
      id: Date.now(),
      type: 'user',
      content: query,
      timestamp: new Date().toISOString()
    };

    setMessages(prev => [...prev, userMessage]);
    setQuery('');
    setIsLoading(true);

    try {
      const response = await axios.post('/api/query', { query: query.trim() });
      
      const botMessage = {
        id: Date.now() + 1,
        type: 'bot',
        content: response.data.response,
        originalContent: response.data.response,
        query: query.trim(),
        confidence: response.data.confidence || 0.85,
        timestamp: new Date().toISOString(),
        feedback: null,
        corrected: false
      };

      setMessages(prev => [...prev, botMessage]);
    } catch (error) {
      console.error('Error querying chatbot:', error);
      const errorMessage = {
        id: Date.now() + 1,
        type: 'error',
        content: 'Failed to get response. Please try again.',
        timestamp: new Date().toISOString()
      };
      setMessages(prev => [...prev, errorMessage]);
    } finally {
      setIsLoading(false);
    }
  };

  const handleFeedback = async (messageId, feedback) => {
    setMessages(prev => prev.map(msg => 
      msg.id === messageId ? { ...msg, feedback } : msg
    ));

    // Save feedback to backend
    const message = messages.find(m => m.id === messageId);
    if (message) {
      try {
        await axios.post('/api/feedback', {
          messageId,
          query: message.query,
          response: message.content,
          feedback,
          timestamp: new Date().toISOString()
        });
      } catch (error) {
        console.error('Error saving feedback:', error);
      }
    }
  };

  const handleStartEdit = (message) => {
    setEditingMessageId(message.id);
    setEditedResponse(message.content);
  };

  const handleCancelEdit = () => {
    setEditingMessageId(null);
    setEditedResponse('');
  };

  const handleSaveCorrection = async (messageId) => {
    const message = messages.find(m => m.id === messageId);
    if (!message || editedResponse.trim() === message.content) {
      handleCancelEdit();
      return;
    }

    setSaveStatus('saving');

    try {
      await axios.post('/api/train', {
        query: message.query,
        originalResponse: message.originalContent,
        correctedResponse: editedResponse.trim(),
        timestamp: new Date().toISOString(),
        confidence: message.confidence
      });

      setMessages(prev => prev.map(msg => 
        msg.id === messageId 
          ? { ...msg, content: editedResponse.trim(), corrected: true }
          : msg
      ));

      setSaveStatus('success');
      setTimeout(() => setSaveStatus(null), 2000);
    } catch (error) {
      console.error('Error saving correction:', error);
      setSaveStatus('error');
      setTimeout(() => setSaveStatus(null), 3000);
    } finally {
      handleCancelEdit();
    }
  };

  const handleRegenerate = async (messageId) => {
    const message = messages.find(m => m.id === messageId);
    if (!message) return;

    setIsLoading(true);

    try {
      const response = await axios.post('/api/query', { 
        query: message.query,
        regenerate: true 
      });

      setMessages(prev => prev.map(msg => 
        msg.id === messageId 
          ? { 
              ...msg, 
              content: response.data.response,
              confidence: response.data.confidence || 0.85,
              corrected: false,
              feedback: null
            }
          : msg
      ));
    } catch (error) {
      console.error('Error regenerating response:', error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-light mb-2 flex items-center gap-3">
          <Sparkles className="w-8 h-8 text-primary-400" />
          Training Mode
        </h1>
        <p className="text-muted">
          Ask questions and correct responses to improve AlBot's knowledge
        </p>
      </div>

      {/* Save Status Toast */}
      {saveStatus && (
        <div className={`fixed top-6 right-6 z-50 flex items-center gap-3 px-6 py-4 rounded-xl shadow-2xl animate-slide-up
          ${saveStatus === 'saving' ? 'bg-dark-card text-light' : ''}
          ${saveStatus === 'success' ? 'bg-success-900/90 text-success-200 border border-success-500/30' : ''}
          ${saveStatus === 'error' ? 'bg-error-900/90 text-error-200 border border-error-500/30' : ''}
        `}>
          {saveStatus === 'saving' && <Loader2 className="w-5 h-5 animate-spin" />}
          {saveStatus === 'success' && <CheckCircle2 className="w-5 h-5" />}
          {saveStatus === 'error' && <AlertCircle className="w-5 h-5" />}
          <span className="font-medium">
            {saveStatus === 'saving' && 'Saving training data...'}
            {saveStatus === 'success' && 'Training data saved successfully!'}
            {saveStatus === 'error' && 'Failed to save. Please try again.'}
          </span>
        </div>
      )}

      {/* Chat Container */}
      <div className="glass-panel p-6 mb-6 min-h-[500px] max-h-[600px] overflow-y-auto">
        {messages.length === 0 ? (
          <div className="h-full flex flex-col items-center justify-center text-center py-12">
            <div className="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-500/20 to-primary-600/20 
                            flex items-center justify-center mb-6 glow">
              <Bot className="w-10 h-10 text-primary-400" />
            </div>
            <h2 className="text-xl font-semibold text-light mb-2">Start Training</h2>
            <p className="text-muted max-w-md">
              Enter a question below to see how AlBot responds. 
              You can then correct the response to improve its knowledge.
            </p>
          </div>
        ) : (
          <div className="space-y-6">
            {messages.map((message) => (
              <div key={message.id} className="animate-slide-up">
                {message.type === 'user' && (
                  <div className="flex justify-end">
                    <div className="chat-bubble chat-bubble-user flex items-start gap-3">
                      <span>{message.content}</span>
                      <User className="w-5 h-5 flex-shrink-0 opacity-70" />
                    </div>
                  </div>
                )}

                {message.type === 'bot' && (
                  <div className="flex flex-col gap-3">
                    <div className="flex items-start gap-3">
                      <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 
                                      flex items-center justify-center flex-shrink-0">
                        <Bot className="w-4 h-4 text-white" />
                      </div>
                      <div className="flex-1">
                        <div className={`chat-bubble chat-bubble-bot ${message.corrected ? 'border-success-500/50' : ''}`}>
                          {editingMessageId === message.id ? (
                            <textarea
                              value={editedResponse}
                              onChange={(e) => setEditedResponse(e.target.value)}
                              className="textarea-field min-h-[120px]"
                              autoFocus
                            />
                          ) : (
                            <p className="whitespace-pre-wrap">{message.content}</p>
                          )}
                          
                          {message.corrected && (
                            <div className="mt-3 pt-3 border-t border-dark-border flex items-center gap-2 text-sm text-success-400">
                              <CheckCircle2 className="w-4 h-4" />
                              <span>Corrected & saved to training data</span>
                            </div>
                          )}
                        </div>

                        {/* Action Buttons */}
                        <div className="flex items-center gap-2 mt-3">
                          {editingMessageId === message.id ? (
                            <>
                              <button
                                onClick={() => handleSaveCorrection(message.id)}
                                className="btn-success text-sm py-2 px-4 flex items-center gap-2"
                              >
                                <Save className="w-4 h-4" />
                                Save Correction
                              </button>
                              <button
                                onClick={handleCancelEdit}
                                className="btn-secondary text-sm py-2 px-4 flex items-center gap-2"
                              >
                                <X className="w-4 h-4" />
                                Cancel
                              </button>
                            </>
                          ) : (
                            <>
                              {/* Thumbs Up/Down */}
                              <button
                                onClick={() => handleFeedback(message.id, 'positive')}
                                className={`p-2 rounded-lg transition-all duration-300
                                  ${message.feedback === 'positive' 
                                    ? 'bg-success-500/20 text-success-400' 
                                    : 'text-muted hover:text-success-400 hover:bg-dark-border'
                                  }`}
                                title="Good response"
                              >
                                <ThumbsUp className="w-4 h-4" />
                              </button>
                              <button
                                onClick={() => handleFeedback(message.id, 'negative')}
                                className={`p-2 rounded-lg transition-all duration-300
                                  ${message.feedback === 'negative' 
                                    ? 'bg-error-500/20 text-error-400' 
                                    : 'text-muted hover:text-error-400 hover:bg-dark-border'
                                  }`}
                                title="Needs improvement"
                              >
                                <ThumbsDown className="w-4 h-4" />
                              </button>

                              {/* Edit Button */}
                              <button
                                onClick={() => handleStartEdit(message)}
                                className="p-2 rounded-lg text-muted hover:text-primary-400 hover:bg-dark-border 
                                           transition-all duration-300"
                                title="Edit response"
                              >
                                <Edit3 className="w-4 h-4" />
                              </button>

                              {/* Regenerate Button */}
                              <button
                                onClick={() => handleRegenerate(message.id)}
                                className="p-2 rounded-lg text-muted hover:text-primary-400 hover:bg-dark-border 
                                           transition-all duration-300"
                                title="Regenerate response"
                                disabled={isLoading}
                              >
                                <RefreshCw className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
                              </button>

                              {/* Confidence Badge */}
                              <div className="ml-auto flex items-center gap-2 text-xs text-muted">
                                <span>Confidence:</span>
                                <span className={`font-medium ${
                                  message.confidence > 0.8 ? 'text-success-400' :
                                  message.confidence > 0.5 ? 'text-warning-400' : 'text-error-400'
                                }`}>
                                  {Math.round(message.confidence * 100)}%
                                </span>
                              </div>
                            </>
                          )}
                        </div>
                      </div>
                    </div>
                  </div>
                )}

                {message.type === 'error' && (
                  <div className="flex items-center gap-3 p-4 bg-error-900/20 border border-error-500/30 rounded-xl text-error-400">
                    <AlertCircle className="w-5 h-5 flex-shrink-0" />
                    <span>{message.content}</span>
                  </div>
                )}
              </div>
            ))}

            {isLoading && (
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 
                                flex items-center justify-center">
                  <Loader2 className="w-4 h-4 text-white animate-spin" />
                </div>
                <div className="chat-bubble chat-bubble-bot">
                  <div className="flex items-center gap-2 text-muted">
                    <span>Thinking</span>
                    <span className="flex gap-1">
                      <span className="w-1.5 h-1.5 bg-primary-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }} />
                      <span className="w-1.5 h-1.5 bg-primary-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }} />
                      <span className="w-1.5 h-1.5 bg-primary-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }} />
                    </span>
                  </div>
                </div>
              </div>
            )}

            <div ref={messagesEndRef} />
          </div>
        )}
      </div>

      {/* Input Form */}
      <form onSubmit={handleSubmit} className="glass-panel p-4">
        <div className="flex gap-4">
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Ask a question to train AlBot..."
            className="input-field flex-1"
            disabled={isLoading}
          />
          <button
            type="submit"
            disabled={!query.trim() || isLoading}
            className="btn-primary flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? (
              <Loader2 className="w-5 h-5 animate-spin" />
            ) : (
              <Send className="w-5 h-5" />
            )}
            <span className="hidden sm:inline">Send</span>
          </button>
        </div>
      </form>
    </div>
  );
}

export default TrainingPage;
