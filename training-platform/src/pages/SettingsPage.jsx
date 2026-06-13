import React, { useState, useEffect } from 'react';
import { 
  Settings, 
  Save, 
  RefreshCw, 
  Database,
  Cpu,
  Shield,
  Bell,
  Moon,
  Sun,
  Key,
  Globe,
  CheckCircle2,
  AlertCircle,
  Trash2
} from 'lucide-react';
import axios from 'axios';

function SettingsPage() {
  const [settings, setSettings] = useState({
    chatbotEndpoint: 'http://localhost:5001/api',
    modelTemperature: 0.7,
    maxTokens: 500,
    autoSaveEnabled: true,
    notificationsEnabled: true,
    darkModeEnabled: true,
    language: 'nl',
    apiKey: ''
  });
  const [isSaving, setIsSaving] = useState(false);
  const [saveStatus, setSaveStatus] = useState(null);
  const [connectionStatus, setConnectionStatus] = useState('checking');

  useEffect(() => {
    checkConnection();
  }, []);

  const checkConnection = async () => {
    setConnectionStatus('checking');
    try {
      // Use our backend proxy to check chatbot status
      const response = await axios.get('/api/chatbot-status', { timeout: 10000 });
      if (response.data.connected && response.data.status === 'healthy') {
        setConnectionStatus('connected');
      } else {
        setConnectionStatus('error');
      }
    } catch (error) {
      setConnectionStatus('error');
    }
  };

  const handleReloadTraining = async () => {
    try {
      const response = await axios.post('/api/reload-chatbot-training');
      if (response.data.success) {
        setSaveStatus('success');
        setTimeout(() => setSaveStatus(null), 3000);
      } else {
        setSaveStatus('error');
        setTimeout(() => setSaveStatus(null), 3000);
      }
    } catch (error) {
      setSaveStatus('error');
      setTimeout(() => setSaveStatus(null), 3000);
    }
  };

  const handleSave = async () => {
    setIsSaving(true);
    try {
      await axios.post('/api/settings', settings);
      setSaveStatus('success');
      setTimeout(() => setSaveStatus(null), 3000);
    } catch (error) {
      console.error('Error saving settings:', error);
      setSaveStatus('error');
      setTimeout(() => setSaveStatus(null), 3000);
    } finally {
      setIsSaving(false);
    }
  };

  const handleClearData = async () => {
    if (!confirm('Are you sure you want to clear all training data? This cannot be undone.')) return;
    
    try {
      await axios.delete('/api/history/all');
      setSaveStatus('success');
    } catch (error) {
      console.error('Error clearing data:', error);
      setSaveStatus('error');
    }
  };

  return (
    <div className="max-w-3xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-light mb-2 flex items-center gap-3">
          <Settings className="w-8 h-8 text-muted" />
          Settings
        </h1>
        <p className="text-muted">
          Configure the training platform and chatbot connection
        </p>
      </div>

      {/* Save Status */}
      {saveStatus && (
        <div className={`mb-6 flex items-center gap-3 px-6 py-4 rounded-xl animate-slide-up
          ${saveStatus === 'success' ? 'bg-success-900/50 text-success-200 border border-success-500/30' : ''}
          ${saveStatus === 'error' ? 'bg-error-900/50 text-error-200 border border-error-500/30' : ''}
        `}>
          {saveStatus === 'success' && <CheckCircle2 className="w-5 h-5" />}
          {saveStatus === 'error' && <AlertCircle className="w-5 h-5" />}
          <span className="font-medium">
            {saveStatus === 'success' && 'Settings saved successfully!'}
            {saveStatus === 'error' && 'Failed to save settings. Please try again.'}
          </span>
        </div>
      )}

      {/* Connection Status */}
      <div className="glass-panel p-6 mb-6">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-4">
            <div className={`w-12 h-12 rounded-xl flex items-center justify-center
              ${connectionStatus === 'connected' ? 'bg-success-500/20' : ''}
              ${connectionStatus === 'error' ? 'bg-error-500/20' : ''}
              ${connectionStatus === 'checking' ? 'bg-dark-card' : ''}
            `}>
              <Database className={`w-6 h-6
                ${connectionStatus === 'connected' ? 'text-success-400' : ''}
                ${connectionStatus === 'error' ? 'text-error-400' : ''}
                ${connectionStatus === 'checking' ? 'text-muted animate-pulse' : ''}
              `} />
            </div>
            <div>
              <h3 className="text-light font-medium">Chatbot Connection</h3>
              <p className={`text-sm
                ${connectionStatus === 'connected' ? 'text-success-400' : ''}
                ${connectionStatus === 'error' ? 'text-error-400' : ''}
                ${connectionStatus === 'checking' ? 'text-muted' : ''}
              `}>
                {connectionStatus === 'connected' && '✓ Connected and healthy'}
                {connectionStatus === 'error' && '✗ Connection failed'}
                {connectionStatus === 'checking' && 'Checking connection...'}
              </p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={handleReloadTraining}
              className="btn-primary py-2 px-4 flex items-center gap-2"
              disabled={connectionStatus !== 'connected'}
            >
              <RefreshCw className="w-4 h-4" />
              Reload Training
            </button>
            <button
              onClick={checkConnection}
              className="btn-secondary py-2 px-4 flex items-center gap-2"
            >
              <RefreshCw className={`w-4 h-4 ${connectionStatus === 'checking' ? 'animate-spin' : ''}`} />
              Retry
            </button>
          </div>
        </div>
      </div>

      {/* API Settings */}
      <div className="glass-panel p-6 mb-6">
        <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
          <Cpu className="w-5 h-5 text-primary-400" />
          API Configuration
        </h2>
        
        <div className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-light mb-2">
              Chatbot Endpoint
            </label>
            <input
              type="text"
              value={settings.chatbotEndpoint}
              onChange={(e) => setSettings({ ...settings, chatbotEndpoint: e.target.value })}
              className="input-field font-mono text-sm"
              placeholder="http://localhost:5001/api"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-light mb-2">
              API Key (optional)
            </label>
            <div className="relative">
              <Key className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" />
              <input
                type="password"
                value={settings.apiKey}
                onChange={(e) => setSettings({ ...settings, apiKey: e.target.value })}
                className="input-field pl-12 font-mono text-sm"
                placeholder="Enter API key for authentication"
              />
            </div>
          </div>

          <div className="grid sm:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-light mb-2">
                Temperature: {settings.modelTemperature}
              </label>
              <input
                type="range"
                min="0"
                max="1"
                step="0.1"
                value={settings.modelTemperature}
                onChange={(e) => setSettings({ ...settings, modelTemperature: parseFloat(e.target.value) })}
                className="w-full accent-primary-500"
              />
              <div className="flex justify-between text-xs text-muted mt-1">
                <span>Precise</span>
                <span>Creative</span>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-light mb-2">
                Max Tokens
              </label>
              <input
                type="number"
                value={settings.maxTokens}
                onChange={(e) => setSettings({ ...settings, maxTokens: parseInt(e.target.value) })}
                className="input-field"
                min="100"
                max="2000"
              />
            </div>
          </div>
        </div>
      </div>

      {/* Preferences */}
      <div className="glass-panel p-6 mb-6">
        <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
          <Shield className="w-5 h-5 text-primary-400" />
          Preferences
        </h2>

        <div className="space-y-4">
          <label className="flex items-center justify-between p-4 bg-dark-card/50 rounded-xl cursor-pointer hover:bg-dark-border/50 transition-colors">
            <div className="flex items-center gap-3">
              <Save className="w-5 h-5 text-muted" />
              <div>
                <span className="text-light font-medium">Auto-save corrections</span>
                <p className="text-sm text-muted">Automatically save training pairs after correction</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={settings.autoSaveEnabled}
              onChange={(e) => setSettings({ ...settings, autoSaveEnabled: e.target.checked })}
              className="w-5 h-5 rounded border-dark-border text-primary-500 focus:ring-primary-500 focus:ring-offset-dark-bg"
            />
          </label>

          <label className="flex items-center justify-between p-4 bg-dark-card/50 rounded-xl cursor-pointer hover:bg-dark-border/50 transition-colors">
            <div className="flex items-center gap-3">
              <Bell className="w-5 h-5 text-muted" />
              <div>
                <span className="text-light font-medium">Notifications</span>
                <p className="text-sm text-muted">Get notified about training milestones</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={settings.notificationsEnabled}
              onChange={(e) => setSettings({ ...settings, notificationsEnabled: e.target.checked })}
              className="w-5 h-5 rounded border-dark-border text-primary-500 focus:ring-primary-500 focus:ring-offset-dark-bg"
            />
          </label>

          <div className="flex items-center justify-between p-4 bg-dark-card/50 rounded-xl">
            <div className="flex items-center gap-3">
              <Globe className="w-5 h-5 text-muted" />
              <div>
                <span className="text-light font-medium">Language</span>
                <p className="text-sm text-muted">Interface language</p>
              </div>
            </div>
            <select
              value={settings.language}
              onChange={(e) => setSettings({ ...settings, language: e.target.value })}
              className="input-field py-2 px-4 w-32"
            >
              <option value="nl">Nederlands</option>
              <option value="en">English</option>
              <option value="de">Deutsch</option>
            </select>
          </div>
        </div>
      </div>

      {/* Danger Zone */}
      <div className="glass-panel p-6 border border-error-500/20">
        <h2 className="text-lg font-semibold text-error-400 mb-6 flex items-center gap-2">
          <AlertCircle className="w-5 h-5" />
          Danger Zone
        </h2>

        <div className="flex items-center justify-between p-4 bg-error-900/20 rounded-xl">
          <div>
            <span className="text-light font-medium">Clear All Training Data</span>
            <p className="text-sm text-muted">Permanently delete all training pairs and corrections</p>
          </div>
          <button
            onClick={handleClearData}
            className="btn-danger py-2 px-4 flex items-center gap-2"
          >
            <Trash2 className="w-4 h-4" />
            Clear Data
          </button>
        </div>
      </div>

      {/* Save Button */}
      <div className="mt-8 flex justify-end">
        <button
          onClick={handleSave}
          disabled={isSaving}
          className="btn-primary flex items-center gap-2"
        >
          {isSaving ? (
            <RefreshCw className="w-5 h-5 animate-spin" />
          ) : (
            <Save className="w-5 h-5" />
          )}
          Save Settings
        </button>
      </div>
    </div>
  );
}

export default SettingsPage;
