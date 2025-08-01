import tkinter as tk
from tkinter import ttk, scrolledtext, messagebox
import json
import os
from datetime import datetime
import threading
import requests
from typing import Dict, List, Any

class ChatbotTrainingFramework:
    def __init__(self):
        self.root = tk.Tk()
        self.root.title("🤖 Nijenhuis Chatbot Training Framework")
        self.root.geometry("1000x750")
        self.root.configure(bg='#1a1a1a')
        
        # Configure grid weights for responsive layout
        self.root.columnconfigure(0, weight=1)
        self.root.rowconfigure(0, weight=1)
        
        # Configure modern styles
        self.setup_styles()
        
        # Training data storage
        self.training_data_file = "training_data.json"
        self.training_data = self.load_training_data()
        
        # API endpoint
        self.api_url = "http://localhost:5001/api/chat"
        
        # Setup UI
        self.setup_ui()
        
    def load_training_data(self) -> Dict[str, Any]:
        """Load existing training data"""
        if os.path.exists(self.training_data_file):
            try:
                with open(self.training_data_file, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except:
                pass
        return {
            "training_sessions": [],
            "improved_responses": {},
            "statistics": {
                "total_tests": 0,
                "improved_responses": 0,
                "accuracy_score": 0.0
            }
        }
    
    def save_training_data(self):
        """Save training data to file"""
        with open(self.training_data_file, 'w', encoding='utf-8') as f:
            json.dump(self.training_data, f, indent=2, ensure_ascii=False)
    
    def setup_ui(self):
        """Setup the original UI design with grid layout"""
        # Main container
        main_frame = ttk.Frame(self.root, padding="10")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        # Configure grid weights
        main_frame.columnconfigure(0, weight=1)
        main_frame.rowconfigure(2, weight=1)
        
        # Title
        title_label = ttk.Label(main_frame, text="🤖 Nijenhuis Chatbot Training Framework", 
                               style='Title.TLabel')
        title_label.grid(row=0, column=0, columnspan=3, pady=(0, 15))
        
        # Input section
        input_frame = ttk.LabelFrame(main_frame, text="📝 Test Input", padding="10", style='Section.TLabelframe')
        input_frame.grid(row=1, column=0, columnspan=3, sticky=(tk.W, tk.E), pady=(0, 10))
        input_frame.columnconfigure(0, weight=1)
        
        # Input field
        ttk.Label(input_frame, text="Enter your test question:", font=('Segoe UI', 11)).grid(row=0, column=0, sticky=tk.W, pady=(0, 5))
        self.input_field = ttk.Entry(input_frame, font=('Segoe UI', 11))
        self.input_field.grid(row=1, column=0, sticky=(tk.W, tk.E), pady=(0, 10))
        self.input_field.bind('<Return>', self.test_chatbot)
        
        # Test buttons
        test_button = ttk.Button(input_frame, text="🚀 Test Chatbot", 
                                command=self.test_chatbot, style='Accent.TButton')
        test_button.grid(row=1, column=1, padx=(10, 0))
        
        quick_test_button = ttk.Button(input_frame, text="⚡ Quick Test", 
                                      command=self.quick_test, style='Accent.TButton')
        quick_test_button.grid(row=1, column=2, padx=(10, 0))
        
        # Results section
        results_frame = ttk.LabelFrame(main_frame, text="📊 Test Results", padding="10", style='Section.TLabelframe')
        results_frame.grid(row=2, column=0, columnspan=3, sticky=(tk.W, tk.E, tk.N, tk.S), pady=(0, 10))
        results_frame.columnconfigure(0, weight=1)
        results_frame.rowconfigure(1, weight=1)
        
        # Response display
        ttk.Label(results_frame, text="Chatbot Response:", font=('Segoe UI', 11)).grid(row=0, column=0, sticky=tk.W, pady=(0, 5))
        self.response_display = scrolledtext.ScrolledText(results_frame, height=8, font=('Segoe UI', 10),
                                                        wrap=tk.WORD, state=tk.DISABLED,
                                                        bg='#2d2d2d', fg='#ffffff',
                                                        insertbackground='#3b82f6',
                                                        selectbackground='#3b82f6',
                                                        selectforeground='#ffffff')
        self.response_display.grid(row=1, column=0, sticky=(tk.W, tk.E, tk.N, tk.S), pady=(0, 10))
        
        # Correction section
        correction_frame = ttk.LabelFrame(results_frame, text="✏️ Response Correction", padding="10", style='Section.TLabelframe')
        correction_frame.grid(row=2, column=0, sticky=(tk.W, tk.E), pady=(0, 10))
        correction_frame.columnconfigure(0, weight=1)
        
        # Correction options frame
        options_frame = ttk.Frame(correction_frame)
        options_frame.grid(row=0, column=0, sticky=(tk.W, tk.E), pady=(0, 10))
        options_frame.columnconfigure(1, weight=1)
        options_frame.columnconfigure(3, weight=1)
        
        # Language correction
        ttk.Label(options_frame, text="Language:", font=('Segoe UI', 11)).grid(row=0, column=0, sticky=tk.W, padx=(0, 5))
        self.language_var = tk.StringVar(value="nl")
        language_combo = ttk.Combobox(options_frame, textvariable=self.language_var, 
                                     values=["nl", "en", "de"], 
                                     state="readonly", width=8, font=('Segoe UI', 10))
        language_combo.grid(row=0, column=1, sticky=tk.W, padx=(0, 20))
        
        # Category correction
        ttk.Label(options_frame, text="Category:", font=('Segoe UI', 11)).grid(row=0, column=2, sticky=tk.W, padx=(0, 5))
        self.category_var = tk.StringVar(value="pricing")
        category_combo = ttk.Combobox(options_frame, textvariable=self.category_var, 
                                     values=["pricing", "opening_hours", "booking", "general", "contact"], 
                                     state="readonly", width=12, font=('Segoe UI', 10))
        category_combo.grid(row=0, column=3, sticky=tk.W)
        
        # Response correction
        ttk.Label(correction_frame, text="Corrected Response (optional):", font=('Segoe UI', 11)).grid(row=1, column=0, sticky=tk.W, pady=(0, 5))
        self.correction_field = scrolledtext.ScrolledText(correction_frame, height=3, font=('Segoe UI', 10),
                                                        wrap=tk.WORD,
                                                        bg='#2d2d2d', fg='#ffffff',
                                                        insertbackground='#3b82f6',
                                                        selectbackground='#3b82f6',
                                                        selectforeground='#ffffff')
        self.correction_field.grid(row=2, column=0, sticky=(tk.W, tk.E), pady=(0, 10))
        
        # Buttons frame
        buttons_frame = ttk.Frame(correction_frame)
        buttons_frame.grid(row=3, column=0, sticky=(tk.W, tk.E))
        
        self.save_button = ttk.Button(buttons_frame, text="💾 Save Correction", 
                                     command=self.save_correction, state=tk.DISABLED, style='Success.TButton')
        self.save_button.pack(side=tk.LEFT, padx=(0, 10))
        
        self.skip_button = ttk.Button(buttons_frame, text="⏭️ Skip", 
                                     command=self.skip_correction, style='Accent.TButton')
        self.skip_button.pack(side=tk.LEFT)
        
        # Statistics section
        stats_frame = ttk.LabelFrame(main_frame, text="📈 Training Statistics", padding="10", style='Section.TLabelframe')
        stats_frame.grid(row=3, column=0, columnspan=3, sticky=(tk.W, tk.E), pady=(0, 10))
        
        # Stats display
        self.stats_text = tk.StringVar()
        self.update_statistics()
        stats_label = ttk.Label(stats_frame, textvariable=self.stats_text, font=('Segoe UI', 11))
        stats_label.grid(row=0, column=0, sticky=tk.W)
        
        # Export and view buttons
        export_button = ttk.Button(stats_frame, text="📤 Export Training Data", 
                                  command=self.export_training_data, style='Accent.TButton')
        export_button.grid(row=0, column=1, padx=(20, 0))
        
        view_data_button = ttk.Button(stats_frame, text="👁️ View Full Data", 
                                     command=self.view_full_training_data, style='Accent.TButton')
        view_data_button.grid(row=0, column=2, padx=(10, 0))
        
        # Training history
        history_frame = ttk.LabelFrame(main_frame, text="📚 Training History", padding="10", style='Section.TLabelframe')
        history_frame.grid(row=4, column=0, columnspan=3, sticky=(tk.W, tk.E, tk.N, tk.S))
        history_frame.columnconfigure(0, weight=1)
        history_frame.rowconfigure(0, weight=1)
        
        # History treeview
        columns = ('Date', 'Question', 'Language', 'Category', 'Status')
        self.history_tree = ttk.Treeview(history_frame, columns=columns, show='headings', height=6)
        
        # Configure column widths
        self.history_tree.column('Date', width=120, minwidth=100)
        self.history_tree.column('Question', width=200, minwidth=150)
        self.history_tree.column('Language', width=80, minwidth=60)
        self.history_tree.column('Category', width=100, minwidth=80)
        self.history_tree.column('Status', width=80, minwidth=60)
        
        for col in columns:
            self.history_tree.heading(col, text=col)
        
        self.history_tree.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        # Scrollbar for history
        history_scrollbar = ttk.Scrollbar(history_frame, orient=tk.VERTICAL, command=self.history_tree.yview)
        history_scrollbar.grid(row=0, column=1, sticky=(tk.N, tk.S))
        self.history_tree.configure(yscrollcommand=history_scrollbar.set)
        
        # History buttons frame
        history_buttons_frame = ttk.Frame(history_frame)
        history_buttons_frame.grid(row=1, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(10, 0))
        
        # Edit button
        self.edit_button = ttk.Button(history_buttons_frame, text="✏️ Edit Selected", 
                                     command=self.edit_selected_item, state=tk.DISABLED, style='Accent.TButton')
        self.edit_button.pack(side=tk.LEFT, padx=(0, 10))
        
        # Delete button
        self.delete_button = ttk.Button(history_buttons_frame, text="🗑️ Delete Selected", 
                                       command=self.delete_selected_item, state=tk.DISABLED, style='Warning.TButton')
        self.delete_button.pack(side=tk.LEFT, padx=(0, 10))
        
        # Refresh button
        refresh_button = ttk.Button(history_buttons_frame, text="🔄 Refresh", 
                                   command=self.load_training_history, style='Accent.TButton')
        refresh_button.pack(side=tk.LEFT)
        
        # Bind selection and double-click events
        self.history_tree.bind('<<TreeviewSelect>>', self.on_history_select)
        self.history_tree.bind('<Double-1>', lambda e: self.edit_selected_item())
        
        # Load history
        self.load_training_history()
        
        # Current test data
        self.current_test = None
    
    def setup_styles(self):
        """Setup modern dark mode styling for the application"""
        style = ttk.Style()
        
        # Configure modern theme
        style.theme_use('clam')
        
        # Modern dark mode colors
        bg_dark = '#0f0f0f'  # Very dark background
        bg_card = '#1a1a1a'  # Card background
        bg_elevated = '#2a2a2a'  # Elevated elements
        bg_input = '#2d2d2d'  # Input fields
        text_primary = '#ffffff'  # Primary text
        text_secondary = '#a0a0a0'  # Secondary text
        accent_blue = '#3b82f6'  # Modern blue
        accent_green = '#10b981'  # Modern green
        accent_red = '#ef4444'  # Modern red
        accent_purple = '#8b5cf6'  # Modern purple
        border_color = '#404040'  # Borders
        border_light = '#333333'  # Light borders
        
        # Configure root window
        self.root.configure(bg=bg_dark)
        
        # Configure all widgets with dark theme
        style.configure('TFrame', background=bg_dark)
        style.configure('TLabel', background=bg_dark, foreground=text_primary)
        style.configure('TButton', 
                       background=bg_elevated, 
                       foreground=text_primary,
                       bordercolor=border_color,
                       focuscolor=accent_blue,
                       font=('Segoe UI', 10))
        
        # Title styling
        style.configure('Title.TLabel', 
                       font=('Segoe UI', 24, 'bold'), 
                       foreground=text_primary,
                       background=bg_dark)
        
        # Section frames
        style.configure('Section.TLabelframe', 
                       font=('Segoe UI', 12, 'bold'),
                       foreground=text_primary,
                       background=bg_card,
                       bordercolor=border_color,
                       lightcolor=border_color,
                       darkcolor=border_color,
                       relief='flat')
        
        style.configure('Section.TLabelframe.Label', 
                       font=('Segoe UI', 12, 'bold'),
                       foreground=text_primary,
                       background=bg_card)
        
        # Modern buttons
        style.configure('Accent.TButton',
                       font=('Segoe UI', 10, 'bold'),
                       background=accent_blue,
                       foreground=text_primary,
                       bordercolor=accent_blue,
                       focuscolor=accent_blue,
                       relief='flat')
        
        style.configure('Success.TButton',
                       font=('Segoe UI', 10, 'bold'),
                       background=accent_green,
                       foreground=text_primary,
                       bordercolor=accent_green,
                       focuscolor=accent_green,
                       relief='flat')
        
        style.configure('Warning.TButton',
                       font=('Segoe UI', 10, 'bold'),
                       background=accent_red,
                       foreground=text_primary,
                       bordercolor=accent_red,
                       focuscolor=accent_red,
                       relief='flat')
        
        style.configure('Purple.TButton',
                       font=('Segoe UI', 10, 'bold'),
                       background=accent_purple,
                       foreground=text_primary,
                       bordercolor=accent_purple,
                       focuscolor=accent_purple,
                       relief='flat')
        
        # Treeview styling
        style.configure('Treeview',
                       font=('Segoe UI', 10),
                       rowheight=30,
                       background=bg_elevated,
                       fieldbackground=bg_elevated,
                       foreground=text_primary,
                       bordercolor=border_color,
                       relief='flat')
        
        style.configure('Treeview.Heading',
                       font=('Segoe UI', 10, 'bold'),
                       background=bg_card,
                       foreground=text_primary,
                       bordercolor=border_color,
                       relief='flat')
        
        # Treeview selection
        style.map('Treeview',
                  background=[('selected', accent_blue)],
                  foreground=[('selected', text_primary)])
        
        style.map('Treeview.Heading',
                  background=[('active', bg_elevated)])
        
        # Entry styling
        style.configure('TEntry',
                       fieldbackground=bg_input,
                       foreground=text_primary,
                       bordercolor=border_color,
                       lightcolor=border_color,
                       darkcolor=border_color,
                       insertcolor=accent_blue,
                       relief='flat')
        
        # Combobox styling
        style.configure('TCombobox',
                       fieldbackground=bg_input,
                       background=bg_input,
                       foreground=text_primary,
                       bordercolor=border_color,
                       lightcolor=border_color,
                       darkcolor=border_color,
                       arrowcolor=text_primary,
                       relief='flat')
        
        # Scrollbar styling
        style.configure('Vertical.TScrollbar',
                       background=bg_elevated,
                       bordercolor=border_color,
                       arrowcolor=text_primary,
                       troughcolor=bg_dark,
                       relief='flat')
        
        # Button hover effects
        style.map('Accent.TButton',
                  background=[('active', '#2563eb')],
                  bordercolor=[('active', '#2563eb')])
        
        style.map('Success.TButton',
                  background=[('active', '#059669')],
                  bordercolor=[('active', '#059669')])
        
        style.map('Warning.TButton',
                  background=[('active', '#dc2626')],
                  bordercolor=[('active', '#dc2626')])
        
        style.map('Purple.TButton',
                  background=[('active', '#7c3aed')],
                  bordercolor=[('active', '#7c3aed')])
    
    def quick_test(self):
        """Quick test with a sample question"""
        self.input_field.delete(0, tk.END)
        self.input_field.insert(0, "Wat kost de Tender 720?")
        self.test_chatbot()
    
    def test_chatbot(self, event=None):
        """Test the chatbot with the input question"""
        question = self.input_field.get().strip()
        if not question:
            messagebox.showwarning("Warning", "Please enter a test question.")
            return
        
        # Disable buttons during testing
        self.save_button.config(state=tk.DISABLED)
        self.skip_button.config(state=tk.DISABLED)
        
        # Show loading message
        self.response_display.config(state=tk.NORMAL)
        self.response_display.delete(1.0, tk.END)
        self.response_display.insert(tk.END, "🔄 Testing chatbot...")
        self.response_display.config(state=tk.DISABLED)
        
        # Test in separate thread
        threading.Thread(target=self._test_chatbot_async, args=(question,), daemon=True).start()
    
    def _test_chatbot_async(self, question: str):
        """Test chatbot asynchronously"""
        try:
            print(f"🔍 Testing question: {question}")
            print(f"🔍 API URL: {self.api_url}")
            
            # Make API call
            response = requests.post(self.api_url, 
                                   json={"message": question},
                                   headers={"Content-Type": "application/json"},
                                   timeout=10)
            
            print(f"🔍 Response status: {response.status_code}")
            print(f"🔍 Response headers: {dict(response.headers)}")
            
            if response.status_code == 200:
                result = response.json()
                print(f"🔍 API Response: {result}")
                
                self.current_test = {
                    "question": question,
                    "original_response": result.get("response", ""),
                    "detected_language": result.get("detected_language", ""),
                    "response_type": result.get("response_type", ""),
                    "timestamp": datetime.now().isoformat()
                }
                
                print(f"🔍 Current test data: {self.current_test}")
                
                # Update UI in main thread
                self.root.after(0, self._update_response_display, result)
            else:
                error_msg = f"API Error: {response.status_code} - {response.text}"
                print(f"❌ API Error: {error_msg}")
                self.root.after(0, self._show_error, error_msg)
                
        except requests.exceptions.RequestException as e:
            error_msg = f"Connection Error: {str(e)}"
            print(f"❌ Connection Error: {error_msg}")
            self.root.after(0, self._show_error, error_msg)
        except Exception as e:
            error_msg = f"Unexpected Error: {str(e)}"
            print(f"❌ Unexpected Error: {error_msg}")
            self.root.after(0, self._show_error, error_msg)
    
    def _update_response_display(self, result: Dict[str, Any]):
        """Update the response display with chatbot result"""
        self.response_display.config(state=tk.NORMAL)
        self.response_display.delete(1.0, tk.END)
        
        # Get the response text
        response_text = result.get('response', 'No response received')
        
        # Build display text - cleaner format
        display_text = f"🤖 CHATBOT RESPONSE:\n"
        display_text += f"{'='*60}\n"
        display_text += f"{response_text}\n\n"
        display_text += f"📊 DETAILS:\n"
        display_text += f"{'='*60}\n"
        display_text += f"🌍 Language: {result.get('detected_language', 'Unknown')}\n"
        display_text += f"📝 Type: {result.get('response_type', 'Unknown')}\n"
        display_text += f"✅ Success: {result.get('success', False)}\n"
        
        # Add training information if available
        if result.get('training_improved'):
            display_text += f"🎯 Training Improved: Yes\n"
        
        self.response_display.insert(tk.END, display_text)
        self.response_display.config(state=tk.DISABLED)
        
        # Enable buttons
        self.save_button.config(state=tk.NORMAL)
        self.skip_button.config(state=tk.NORMAL)
        
        # Clear correction field and pre-fill with current response for easy editing
        self.correction_field.delete(1.0, tk.END)
        self.correction_field.insert(tk.END, response_text)
        
        # Set language and category dropdowns based on detected values
        detected_language = result.get('detected_language', 'nl')
        detected_category = result.get('response_type', 'pricing')
        
        self.language_var.set(detected_language)
        self.category_var.set(detected_category)
    
    def _show_error(self, error_msg: str):
        """Show error message"""
        self.response_display.config(state=tk.NORMAL)
        self.response_display.delete(1.0, tk.END)
        self.response_display.insert(tk.END, f"❌ Error: {error_msg}")
        self.response_display.config(state=tk.DISABLED)
        
        # Enable buttons
        self.save_button.config(state=tk.NORMAL)
        self.skip_button.config(state=tk.NORMAL)
    
    def save_correction(self):
        """Save the corrected response with language and category"""
        if not self.current_test:
            messagebox.showwarning("Warning", "No test to save correction for.")
            return
        
        corrected_response = self.correction_field.get(1.0, tk.END).strip()
        if not corrected_response:
            messagebox.showwarning("Warning", "Please enter a corrected response.")
            return
        
        # Get corrected language and category
        corrected_language = self.language_var.get()
        corrected_category = self.category_var.get()
        
        # Add correction to current test
        self.current_test["corrected_response"] = corrected_response
        self.current_test["corrected_language"] = corrected_language
        self.current_test["corrected_category"] = corrected_category
        self.current_test["status"] = "Corrected"
        
        # Save to training data
        self.training_data["training_sessions"].append(self.current_test)
        
        # Update improved responses mapping
        question_key = self.current_test["question"].lower()
        self.training_data["improved_responses"][question_key] = {
            "original": self.current_test["original_response"],
            "corrected": corrected_response,
            "language": corrected_language,
            "response_type": corrected_category,
            "timestamp": self.current_test["timestamp"]
        }
        
        # Update statistics
        self.training_data["statistics"]["total_tests"] += 1
        self.training_data["statistics"]["improved_responses"] += 1
        
        # Save data
        self.save_training_data()
        
        # Update UI
        self.update_statistics()
        self.load_training_history()
        
        # Clear fields
        self.input_field.delete(0, tk.END)
        self.correction_field.delete(1.0, tk.END)
        self.response_display.config(state=tk.NORMAL)
        self.response_display.delete(1.0, tk.END)
        self.response_display.config(state=tk.DISABLED)
        
        # Disable buttons
        self.save_button.config(state=tk.DISABLED)
        self.skip_button.config(state=tk.DISABLED)
        
        self.current_test = None
        
        messagebox.showinfo("Success", "Correction saved successfully!")
    
    def skip_correction(self):
        """Skip the current correction"""
        if not self.current_test:
            return
        
        # Mark as skipped
        self.current_test["corrected_response"] = ""
        self.current_test["status"] = "Skipped"
        
        # Save to training data
        self.training_data["training_sessions"].append(self.current_test)
        
        # Update statistics
        self.training_data["statistics"]["total_tests"] += 1
        
        # Save data
        self.save_training_data()
        
        # Update UI
        self.update_statistics()
        self.load_training_history()
        
        # Clear fields
        self.input_field.delete(0, tk.END)
        self.correction_field.delete(1.0, tk.END)
        self.response_display.config(state=tk.NORMAL)
        self.response_display.delete(1.0, tk.END)
        self.response_display.config(state=tk.DISABLED)
        
        # Disable buttons
        self.save_button.config(state=tk.DISABLED)
        self.skip_button.config(state=tk.DISABLED)
        
        self.current_test = None
    
    def update_statistics(self):
        """Update the statistics display"""
        stats = self.training_data["statistics"]
        total = stats["total_tests"]
        improved = stats["improved_responses"]
        accuracy = (improved / total * 100) if total > 0 else 0
        
        stats_text = f"📊 Total Tests: {total}  |  ✏️ Improved Responses: {improved}  |  🎯 Accuracy: {accuracy:.1f}%"
        self.stats_text.set(stats_text)
    
    def on_history_select(self, event):
        """Handle history item selection"""
        selection = self.history_tree.selection()
        if selection:
            self.edit_button.config(state=tk.NORMAL)
            self.delete_button.config(state=tk.NORMAL)
        else:
            self.edit_button.config(state=tk.DISABLED)
            self.delete_button.config(state=tk.DISABLED)
    
    def load_training_history(self):
        """Load training history into the treeview"""
        # Clear existing items
        for item in self.history_tree.get_children():
            self.history_tree.delete(item)
        
        # Add recent sessions (last 50)
        sessions = self.training_data["training_sessions"][-50:]
        for i, session in enumerate(sessions):
            date = session["timestamp"][:19].replace("T", " ")  # Format date
            question = session["question"][:50] + "..." if len(session["question"]) > 50 else session["question"]
            
            # Get language and category (use corrected if available, otherwise detected)
            language = session.get("corrected_language", session.get("detected_language", "Unknown"))
            category = session.get("corrected_category", session.get("response_type", "Unknown"))
            status = session.get("status", "Unknown")
            
            # Store the session index as item ID for easy retrieval
            item_id = f"session_{len(sessions) - 1 - i}"  # Reverse order to match display
            self.history_tree.insert("", tk.END, iid=item_id, values=(date, question, language, category, status))
    
    def get_selected_session(self):
        """Get the selected session data"""
        selection = self.history_tree.selection()
        if not selection:
            return None
        
        item_id = selection[0]
        # Extract session index from item ID
        try:
            session_index = int(item_id.split('_')[1])
            # Convert to actual index in training_sessions (reverse order)
            sessions = self.training_data["training_sessions"][-50:]
            actual_index = len(self.training_data["training_sessions"]) - 1 - session_index
            return actual_index, self.training_data["training_sessions"][actual_index]
        except (IndexError, ValueError):
            return None
    
    def edit_selected_item(self):
        """Edit the selected training item"""
        session_data = self.get_selected_session()
        if not session_data:
            messagebox.showwarning("Warning", "Please select an item to edit.")
            return
        
        index, session = session_data
        self.show_edit_dialog(index, session)
    
    def delete_selected_item(self):
        """Delete the selected training item"""
        session_data = self.get_selected_session()
        if not session_data:
            messagebox.showwarning("Warning", "Please select an item to delete.")
            return
        
        index, session = session_data
        
        # Confirm deletion
        question = session["question"][:50] + "..." if len(session["question"]) > 50 else session["question"]
        result = messagebox.askyesno("Confirm Delete", 
                                   f"Are you sure you want to delete this training item?\n\nQuestion: {question}")
        
        if result:
            # Remove from training_sessions
            del self.training_data["training_sessions"][index]
            
            # Update improved_responses if this was a corrected response
            if session.get("corrected_response"):
                question_key = session["question"].lower()
                if question_key in self.training_data["improved_responses"]:
                    del self.training_data["improved_responses"][question_key]
            
            # Update statistics
            self.training_data["statistics"]["total_tests"] -= 1
            if session.get("corrected_response"):
                self.training_data["statistics"]["improved_responses"] -= 1
            
            # Save and refresh
            self.save_training_data()
            self.update_statistics()
            self.load_training_history()
            
            messagebox.showinfo("Success", "Training item deleted successfully!")
    
    def show_edit_dialog(self, index, session):
        """Show dialog to edit training data"""
        # Create edit dialog
        edit_window = tk.Toplevel(self.root)
        edit_window.title("✏️ Edit Training Data")
        edit_window.geometry("800x600")
        edit_window.configure(bg='#1a1a1a')
        
        # Make dialog modal
        edit_window.transient(self.root)
        edit_window.grab_set()
        
        # Main frame
        main_frame = ttk.Frame(edit_window, padding="20")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        # Configure grid weights
        edit_window.columnconfigure(0, weight=1)
        edit_window.rowconfigure(0, weight=1)
        main_frame.columnconfigure(1, weight=1)
        
        # Title
        title_label = ttk.Label(main_frame, text="✏️ Edit Training Data", 
                               font=('Segoe UI', 18, 'bold'))
        title_label.grid(row=0, column=0, columnspan=2, pady=(0, 20))
        
        # Question field
        ttk.Label(main_frame, text="Question:", font=('Segoe UI', 11)).grid(row=1, column=0, sticky=tk.W, pady=(0, 5))
        question_entry = ttk.Entry(main_frame, font=('Segoe UI', 11), width=60)
        question_entry.grid(row=1, column=1, sticky=(tk.W, tk.E), pady=(0, 15))
        question_entry.insert(0, session["question"])
        
        # Original response
        ttk.Label(main_frame, text="Original Response:", font=('Segoe UI', 11)).grid(row=2, column=0, sticky=tk.W, pady=(0, 5))
        original_text = scrolledtext.ScrolledText(main_frame, height=4, font=('Segoe UI', 10), wrap=tk.WORD,
                                                bg='#2d2d2d', fg='#ffffff',
                                                insertbackground='#3b82f6',
                                                selectbackground='#3b82f6',
                                                selectforeground='#ffffff')
        original_text.grid(row=2, column=1, sticky=(tk.W, tk.E), pady=(0, 15))
        original_text.insert(tk.END, session["original_response"])
        
        # Corrected response
        ttk.Label(main_frame, text="Corrected Response:", font=('Segoe UI', 11)).grid(row=3, column=0, sticky=tk.W, pady=(0, 5))
        corrected_text = scrolledtext.ScrolledText(main_frame, height=4, font=('Segoe UI', 10), wrap=tk.WORD,
                                                 bg='#2d2d2d', fg='#ffffff',
                                                 insertbackground='#3b82f6',
                                                 selectbackground='#3b82f6',
                                                 selectforeground='#ffffff')
        corrected_text.grid(row=3, column=1, sticky=(tk.W, tk.E), pady=(0, 15))
        corrected_text.insert(tk.END, session.get("corrected_response", ""))
        
        # Language and type
        info_frame = ttk.Frame(main_frame)
        info_frame.grid(row=4, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 15))
        
        ttk.Label(info_frame, text="Language:", font=('Segoe UI', 11)).grid(row=0, column=0, sticky=tk.W, padx=(0, 10))
        language_var = tk.StringVar(value=session.get("corrected_language", session.get("detected_language", "nl")))
        language_combo = ttk.Combobox(info_frame, textvariable=language_var, 
                                    values=["nl", "en", "de"], state="readonly", width=10)
        language_combo.grid(row=0, column=1, padx=(0, 20))
        
        ttk.Label(info_frame, text="Category:", font=('Segoe UI', 11)).grid(row=0, column=2, sticky=tk.W, padx=(0, 10))
        category_var = tk.StringVar(value=session.get("corrected_category", session.get("response_type", "pricing")))
        category_combo = ttk.Combobox(info_frame, textvariable=category_var, 
                                    values=["pricing", "opening_hours", "booking", "general", "contact"], 
                                    state="readonly", width=15)
        category_combo.grid(row=0, column=3)
        
        # Buttons
        button_frame = ttk.Frame(main_frame)
        button_frame.grid(row=5, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(20, 0))
        
        def save_changes():
            """Save the edited training data"""
            try:
                # Update session data
                session["question"] = question_entry.get().strip()
                session["original_response"] = original_text.get(1.0, tk.END).strip()
                session["corrected_response"] = corrected_text.get(1.0, tk.END).strip()
                session["corrected_language"] = language_var.get()
                session["corrected_category"] = category_var.get()
                
                # Update improved_responses if there's a corrected response
                if session["corrected_response"]:
                    question_key = session["question"].lower()
                    self.training_data["improved_responses"][question_key] = {
                        "original": session["original_response"],
                        "corrected": session["corrected_response"],
                        "language": session["corrected_language"],
                        "response_type": session["corrected_category"],
                        "timestamp": session["timestamp"]
                    }
                    session["status"] = "Corrected"
                else:
                    session["status"] = "Skipped"
                
                # Save and refresh
                self.save_training_data()
                self.update_statistics()
                self.load_training_history()
                
                edit_window.destroy()
                messagebox.showinfo("Success", "Training data updated successfully!")
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to save changes: {str(e)}")
        
        def cancel_edit():
            """Cancel editing"""
            edit_window.destroy()
        
        save_button = ttk.Button(button_frame, text="💾 Save Changes", command=save_changes)
        save_button.pack(side=tk.LEFT, padx=(0, 10))
        
        cancel_button = ttk.Button(button_frame, text="❌ Cancel", command=cancel_edit)
        cancel_button.pack(side=tk.LEFT)
    
    def view_full_training_data(self):
        """View the complete training data in a new window"""
        # Create view window
        view_window = tk.Toplevel(self.root)
        view_window.title("👁️ Full Training Data")
        view_window.geometry("1000x700")
        view_window.configure(bg='#f5f5f5')
        
        # Make dialog modal
        view_window.transient(self.root)
        view_window.grab_set()
        
        # Main frame
        main_frame = ttk.Frame(view_window, padding="20")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        # Configure grid weights
        view_window.columnconfigure(0, weight=1)
        view_window.rowconfigure(0, weight=1)
        main_frame.columnconfigure(0, weight=1)
        main_frame.rowconfigure(1, weight=1)
        
        # Title
        title_label = ttk.Label(main_frame, text="👁️ Complete Training Data", 
                               font=('Arial', 16, 'bold'))
        title_label.grid(row=0, column=0, pady=(0, 20))
        
        # Text area for displaying data
        text_area = scrolledtext.ScrolledText(main_frame, font=('Courier', 10), wrap=tk.WORD)
        text_area.grid(row=1, column=0, sticky=(tk.W, tk.E, tk.N, tk.S), pady=(0, 20))
        
        # Format and display the training data
        formatted_data = json.dumps(self.training_data, indent=2, ensure_ascii=False)
        text_area.insert(tk.END, formatted_data)
        text_area.config(state=tk.DISABLED)
        
        # Close button
        close_button = ttk.Button(main_frame, text="❌ Close", 
                                 command=view_window.destroy)
        close_button.grid(row=2, column=0)
    
    def export_training_data(self):
        """Export training data to a file"""
        try:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"training_data_export_{timestamp}.json"
            
            with open(filename, 'w', encoding='utf-8') as f:
                json.dump(self.training_data, f, indent=2, ensure_ascii=False)
            
            messagebox.showinfo("Export Success", f"Training data exported to {filename}")
        except Exception as e:
            messagebox.showerror("Export Error", f"Failed to export data: {str(e)}")
    
    def run(self):
        """Run the training framework"""
        self.root.mainloop()

def main():
    """Main function to run the training framework"""
    app = ChatbotTrainingFramework()
    app.run()

if __name__ == "__main__":
    main() 