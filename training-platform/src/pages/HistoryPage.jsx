import React, { useState, useEffect } from 'react';
import { 
  History, 
  Search, 
  Filter, 
  Download, 
  Upload,
  Trash2,
  Eye,
  CheckCircle2,
  XCircle,
  Clock,
  MessageSquare,
  ChevronDown,
  ChevronUp,
  FileJson
} from 'lucide-react';
import axios from 'axios';
import { format } from 'date-fns';

function HistoryPage() {
  const [sessions, setSessions] = useState([]);
  const [filteredSessions, setFilteredSessions] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [filterType, setFilterType] = useState('all');
  const [expandedSession, setExpandedSession] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    fetchSessions();
  }, []);

  useEffect(() => {
    filterSessions();
  }, [sessions, searchQuery, filterType]);

  const fetchSessions = async () => {
    try {
      const response = await axios.get('/api/history');
      setSessions(response.data.sessions || []);
    } catch (error) {
      console.error('Error fetching history:', error);
      // Mock data for demo
      setSessions([
        {
          id: '1',
          query: 'Wat kost de tender 720?',
          originalResponse: 'De boot kost geld.',
          correctedResponse: 'De Classic Tender 720 kost €230 per dag. Er is een borg van €100.',
          feedback: 'positive',
          timestamp: new Date().toISOString(),
          confidence: 0.45
        },
        {
          id: '2', 
          query: 'Wanneer zijn jullie open?',
          originalResponse: 'Wij zijn open.',
          correctedResponse: 'Wij zijn geopend van 1 april tot 31 oktober, dagelijks van 9:00 - 18:00.',
          feedback: 'positive',
          timestamp: new Date(Date.now() - 86400000).toISOString(),
          confidence: 0.55
        },
        {
          id: '3',
          query: 'Mag mijn hond mee?',
          originalResponse: 'Huisdieren zijn niet toegestaan.',
          correctedResponse: null,
          feedback: 'positive',
          timestamp: new Date(Date.now() - 172800000).toISOString(),
          confidence: 0.92
        }
      ]);
    } finally {
      setIsLoading(false);
    }
  };

  const filterSessions = () => {
    let filtered = [...sessions];

    if (searchQuery) {
      const query = searchQuery.toLowerCase();
      filtered = filtered.filter(s => 
        s.query.toLowerCase().includes(query) ||
        s.originalResponse?.toLowerCase().includes(query) ||
        s.correctedResponse?.toLowerCase().includes(query)
      );
    }

    if (filterType === 'corrected') {
      filtered = filtered.filter(s => s.correctedResponse);
    } else if (filterType === 'uncorrected') {
      filtered = filtered.filter(s => !s.correctedResponse);
    } else if (filterType === 'positive') {
      filtered = filtered.filter(s => s.feedback === 'positive');
    } else if (filterType === 'negative') {
      filtered = filtered.filter(s => s.feedback === 'negative');
    }

    setFilteredSessions(filtered);
  };

  const handleExport = () => {
    const data = JSON.stringify(sessions, null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `albot-training-data-${format(new Date(), 'yyyy-MM-dd')}.json`;
    a.click();
    URL.revokeObjectURL(url);
  };

  const handleImport = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async (event) => {
      try {
        const data = JSON.parse(event.target?.result);
        await axios.post('/api/import', { data });
        fetchSessions();
      } catch (error) {
        console.error('Error importing data:', error);
      }
    };
    reader.readAsText(file);
  };

  const handleDelete = async (id) => {
    if (!confirm('Are you sure you want to delete this training pair?')) return;

    try {
      await axios.delete(`/api/history/${id}`);
      setSessions(prev => prev.filter(s => s.id !== id));
    } catch (error) {
      console.error('Error deleting session:', error);
    }
  };

  const stats = {
    total: sessions.length,
    corrected: sessions.filter(s => s.correctedResponse).length,
    positive: sessions.filter(s => s.feedback === 'positive').length,
    negative: sessions.filter(s => s.feedback === 'negative').length
  };

  return (
    <div className="max-w-6xl mx-auto">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 className="text-3xl font-bold text-light mb-2 flex items-center gap-3">
            <History className="w-8 h-8 text-primary-400" />
            Training History
          </h1>
          <p className="text-muted">
            View and manage all training data and corrections
          </p>
        </div>

        <div className="flex items-center gap-3">
          <label className="btn-secondary text-sm py-2 px-4 flex items-center gap-2 cursor-pointer">
            <Upload className="w-4 h-4" />
            Import
            <input
              type="file"
              accept=".json"
              onChange={handleImport}
              className="hidden"
            />
          </label>
          <button
            onClick={handleExport}
            className="btn-primary text-sm py-2 px-4 flex items-center gap-2"
          >
            <Download className="w-4 h-4" />
            Export
          </button>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div className="stat-card">
          <MessageSquare className="w-8 h-8 text-primary-400 mb-2" />
          <span className="text-2xl font-bold text-light">{stats.total}</span>
          <span className="text-sm text-muted">Total Pairs</span>
        </div>
        <div className="stat-card">
          <CheckCircle2 className="w-8 h-8 text-success-400 mb-2" />
          <span className="text-2xl font-bold text-light">{stats.corrected}</span>
          <span className="text-sm text-muted">Corrected</span>
        </div>
        <div className="stat-card">
          <div className="w-8 h-8 text-success-400 mb-2 flex items-center justify-center">👍</div>
          <span className="text-2xl font-bold text-light">{stats.positive}</span>
          <span className="text-sm text-muted">Positive</span>
        </div>
        <div className="stat-card">
          <div className="w-8 h-8 text-error-400 mb-2 flex items-center justify-center">👎</div>
          <span className="text-2xl font-bold text-light">{stats.negative}</span>
          <span className="text-sm text-muted">Negative</span>
        </div>
      </div>

      {/* Search and Filter */}
      <div className="glass-panel p-4 mb-6">
        <div className="flex flex-col sm:flex-row gap-4">
          <div className="relative flex-1">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search queries or responses..."
              className="input-field pl-12"
            />
          </div>
          <div className="relative">
            <Filter className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" />
            <select
              value={filterType}
              onChange={(e) => setFilterType(e.target.value)}
              className="input-field pl-12 pr-10 appearance-none cursor-pointer min-w-[180px]"
            >
              <option value="all">All</option>
              <option value="corrected">Corrected</option>
              <option value="uncorrected">Uncorrected</option>
              <option value="positive">Positive Feedback</option>
              <option value="negative">Negative Feedback</option>
            </select>
          </div>
        </div>
      </div>

      {/* Sessions List */}
      <div className="space-y-4">
        {isLoading ? (
          <div className="glass-panel p-12 text-center">
            <div className="animate-spin w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full mx-auto mb-4" />
            <p className="text-muted">Loading history...</p>
          </div>
        ) : filteredSessions.length === 0 ? (
          <div className="glass-panel p-12 text-center">
            <FileJson className="w-12 h-12 text-muted mx-auto mb-4" />
            <h3 className="text-lg font-medium text-light mb-2">No training data found</h3>
            <p className="text-muted">
              {searchQuery ? 'Try a different search query' : 'Start training to see your history here'}
            </p>
          </div>
        ) : (
          filteredSessions.map((session) => (
            <div key={session.id} className="glass-panel overflow-hidden">
              {/* Session Header */}
              <button
                onClick={() => setExpandedSession(expandedSession === session.id ? null : session.id)}
                className="w-full p-4 flex items-center gap-4 hover:bg-dark-border/30 transition-colors"
              >
                <div className="flex-1 text-left">
                  <p className="text-light font-medium mb-1 line-clamp-1">{session.query}</p>
                  <div className="flex items-center gap-4 text-sm text-muted">
                    <span className="flex items-center gap-1">
                      <Clock className="w-4 h-4" />
                      {format(new Date(session.timestamp), 'MMM d, yyyy HH:mm')}
                    </span>
                    {session.correctedResponse && (
                      <span className="flex items-center gap-1 text-success-400">
                        <CheckCircle2 className="w-4 h-4" />
                        Corrected
                      </span>
                    )}
                    {session.feedback === 'positive' && (
                      <span className="text-success-400">👍</span>
                    )}
                    {session.feedback === 'negative' && (
                      <span className="text-error-400">👎</span>
                    )}
                  </div>
                </div>
                {expandedSession === session.id ? (
                  <ChevronUp className="w-5 h-5 text-muted" />
                ) : (
                  <ChevronDown className="w-5 h-5 text-muted" />
                )}
              </button>

              {/* Expanded Content */}
              {expandedSession === session.id && (
                <div className="px-4 pb-4 space-y-4 border-t border-dark-border pt-4 animate-fade-in">
                  <div>
                    <h4 className="text-sm font-medium text-muted mb-2">Original Response</h4>
                    <p className={`p-3 rounded-lg bg-dark-card text-light ${session.correctedResponse ? 'diff-removed' : ''}`}>
                      {session.originalResponse}
                    </p>
                  </div>

                  {session.correctedResponse && (
                    <div>
                      <h4 className="text-sm font-medium text-muted mb-2">Corrected Response</h4>
                      <p className="p-3 rounded-lg bg-dark-card text-light diff-added">
                        {session.correctedResponse}
                      </p>
                    </div>
                  )}

                  <div className="flex items-center justify-between pt-2">
                    <span className="text-sm text-muted">
                      Confidence: {Math.round(session.confidence * 100)}%
                    </span>
                    <button
                      onClick={() => handleDelete(session.id)}
                      className="flex items-center gap-2 text-sm text-error-400 hover:text-error-300 transition-colors"
                    >
                      <Trash2 className="w-4 h-4" />
                      Delete
                    </button>
                  </div>
                </div>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
}

export default HistoryPage;
