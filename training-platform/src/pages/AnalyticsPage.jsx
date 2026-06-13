import React, { useState, useEffect } from 'react';
import { 
  BarChart3, 
  TrendingUp, 
  Target, 
  Zap,
  Calendar,
  MessageSquare,
  CheckCircle2,
  AlertTriangle,
  Award
} from 'lucide-react';
import axios from 'axios';
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Legend
} from 'recharts';

// Updated colors to match brand
const COLORS = ['#8049cc', '#6643c5', '#4CAF50', '#FF9800', '#F44336'];

function AnalyticsPage() {
  const [analytics, setAnalytics] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [timeRange, setTimeRange] = useState('7d');

  useEffect(() => {
    fetchAnalytics();
  }, [timeRange]);

  const fetchAnalytics = async () => {
    try {
      const response = await axios.get(`/api/analytics?range=${timeRange}`);
      setAnalytics(response.data);
    } catch (error) {
      console.error('Error fetching analytics:', error);
      // Mock data for demo
      setAnalytics({
        overview: {
          totalQueries: 156,
          totalCorrections: 42,
          avgConfidence: 0.78,
          improvementRate: 0.27
        },
        accuracyTrend: [
          { date: 'Mon', accuracy: 72, queries: 18 },
          { date: 'Tue', accuracy: 75, queries: 22 },
          { date: 'Wed', accuracy: 78, queries: 25 },
          { date: 'Thu', accuracy: 82, queries: 20 },
          { date: 'Fri', accuracy: 85, queries: 28 },
          { date: 'Sat', accuracy: 88, queries: 23 },
          { date: 'Sun', accuracy: 91, queries: 20 }
        ],
        topicDistribution: [
          { name: 'Pricing', value: 35, color: '#8049cc' },
          { name: 'Booking', value: 25, color: '#6643c5' },
          { name: 'Hours', value: 18, color: '#4CAF50' },
          { name: 'Location', value: 12, color: '#FF9800' },
          { name: 'Other', value: 10, color: '#F44336' }
        ],
        feedbackBreakdown: [
          { name: 'Positive', value: 72 },
          { name: 'Negative', value: 18 },
          { name: 'Neutral', value: 10 }
        ],
        topCorrectedTopics: [
          { topic: 'Multi-day Pricing', corrections: 15 },
          { topic: 'Boat Availability', corrections: 12 },
          { topic: 'Deposit Info', corrections: 8 },
          { topic: 'Pet Policy', corrections: 5 },
          { topic: 'Giethoorn Access', corrections: 2 }
        ]
      });
    } finally {
      setIsLoading(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="text-center">
          <div className="animate-spin w-12 h-12 border-3 border-primary-500 border-t-transparent rounded-full mx-auto mb-4" />
          <p className="text-muted">Loading analytics...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h1 className="text-3xl font-bold text-light mb-2 flex items-center gap-3">
            <BarChart3 className="w-8 h-8 text-primary-400" />
            Analytics Dashboard
          </h1>
          <p className="text-muted">
            Track training progress and model improvements
          </p>
        </div>

        <div className="flex items-center gap-2">
          <Calendar className="w-5 h-5 text-muted" />
          <select
            value={timeRange}
            onChange={(e) => setTimeRange(e.target.value)}
            className="input-field py-2 px-4"
          >
            <option value="7d">Last 7 days</option>
            <option value="30d">Last 30 days</option>
            <option value="90d">Last 90 days</option>
            <option value="all">All time</option>
          </select>
        </div>
      </div>

      {/* Overview Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div className="stat-card gradient-border glow">
          <MessageSquare className="w-10 h-10 text-primary-400 mb-3" />
          <span className="text-3xl font-bold text-light mb-1">
            {analytics.overview.totalQueries}
          </span>
          <span className="text-sm text-muted">Total Queries</span>
        </div>

        <div className="stat-card gradient-border">
          <CheckCircle2 className="w-10 h-10 text-success-400 mb-3" />
          <span className="text-3xl font-bold text-light mb-1">
            {analytics.overview.totalCorrections}
          </span>
          <span className="text-sm text-muted">Corrections Made</span>
        </div>

        <div className="stat-card gradient-border">
          <Target className="w-10 h-10 text-primary-400 mb-3" />
          <span className="text-3xl font-bold text-light mb-1">
            {Math.round(analytics.overview.avgConfidence * 100)}%
          </span>
          <span className="text-sm text-muted">Avg Confidence</span>
        </div>

        <div className="stat-card gradient-border">
          <TrendingUp className="w-10 h-10 text-warning-400 mb-3" />
          <span className="text-3xl font-bold text-light mb-1">
            +{Math.round(analytics.overview.improvementRate * 100)}%
          </span>
          <span className="text-sm text-muted">Improvement</span>
        </div>
      </div>

      {/* Charts Row */}
      <div className="grid lg:grid-cols-2 gap-6 mb-8">
        {/* Accuracy Trend */}
        <div className="glass-panel p-6">
          <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
            <TrendingUp className="w-5 h-5 text-primary-400" />
            Accuracy Trend
          </h2>
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={analytics.accuracyTrend}>
                <CartesianGrid strokeDasharray="3 3" stroke="#3D3D3D" />
                <XAxis dataKey="date" stroke="#808080" fontSize={12} />
                <YAxis stroke="#808080" fontSize={12} domain={[0, 100]} />
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1A1A1A',
                    border: '1px solid #3D3D3D',
                    borderRadius: '12px',
                    color: '#EFEAF3'
                  }}
                />
                <Line
                  type="monotone"
                  dataKey="accuracy"
                  stroke="#8049cc"
                  strokeWidth={3}
                  dot={{ fill: '#8049cc', strokeWidth: 2 }}
                  activeDot={{ r: 6, fill: '#8049cc' }}
                />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Topic Distribution */}
        <div className="glass-panel p-6">
          <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
            <Zap className="w-5 h-5 text-primary-400" />
            Topic Distribution
          </h2>
          <div className="h-64 flex items-center justify-center">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={analytics.topicDistribution}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={90}
                  paddingAngle={3}
                  dataKey="value"
                >
                  {analytics.topicDistribution.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color || COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1A1A1A',
                    border: '1px solid #3D3D3D',
                    borderRadius: '12px',
                    color: '#EFEAF3'
                  }}
                />
                <Legend
                  verticalAlign="middle"
                  align="right"
                  layout="vertical"
                  wrapperStyle={{ paddingLeft: '20px' }}
                />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      {/* Bottom Row */}
      <div className="grid lg:grid-cols-2 gap-6">
        {/* Query Volume */}
        <div className="glass-panel p-6">
          <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
            <MessageSquare className="w-5 h-5 text-primary-400" />
            Query Volume
          </h2>
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={analytics.accuracyTrend}>
                <CartesianGrid strokeDasharray="3 3" stroke="#3D3D3D" />
                <XAxis dataKey="date" stroke="#808080" fontSize={12} />
                <YAxis stroke="#808080" fontSize={12} />
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1A1A1A',
                    border: '1px solid #3D3D3D',
                    borderRadius: '12px',
                    color: '#EFEAF3'
                  }}
                />
                <Bar
                  dataKey="queries"
                  fill="#6643c5"
                  radius={[4, 4, 0, 0]}
                />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Most Corrected Topics */}
        <div className="glass-panel p-6">
          <h2 className="text-lg font-semibold text-light mb-6 flex items-center gap-2">
            <AlertTriangle className="w-5 h-5 text-warning-400" />
            Topics Needing Most Improvement
          </h2>
          <div className="space-y-4">
            {analytics.topCorrectedTopics.map((topic, index) => (
              <div key={topic.topic} className="flex items-center gap-4">
                <span className="w-6 h-6 rounded-full bg-dark-card flex items-center justify-center text-sm font-medium text-muted">
                  {index + 1}
                </span>
                <div className="flex-1">
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-light font-medium">{topic.topic}</span>
                    <span className="text-sm text-muted">{topic.corrections} corrections</span>
                  </div>
                  <div className="h-2 bg-dark-card rounded-full overflow-hidden">
                    <div
                      className="h-full bg-gradient-to-r from-warning-500 to-warning-600 rounded-full"
                      style={{ width: `${(topic.corrections / analytics.topCorrectedTopics[0].corrections) * 100}%` }}
                    />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Achievement Banner */}
      <div className="mt-8 glass-panel p-6 gradient-border glow">
        <div className="flex items-center gap-6">
          <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 
                          flex items-center justify-center shadow-lg shadow-primary-500/25">
            <Award className="w-8 h-8 text-white" />
          </div>
          <div>
            <h3 className="text-xl font-bold text-light mb-1">Training Milestone Reached!</h3>
            <p className="text-muted">
              You've made {analytics.overview.totalCorrections} corrections, improving accuracy by {Math.round(analytics.overview.improvementRate * 100)}%.
              Keep training to reach the next milestone!
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default AnalyticsPage;
