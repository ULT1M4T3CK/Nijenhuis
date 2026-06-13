import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import { 
  MessageSquare, 
  History, 
  BarChart3, 
  Settings, 
  Menu,
  X,
  Bot,
  Sparkles
} from 'lucide-react';

const navItems = [
  { path: '/', label: 'Training', icon: MessageSquare },
  { path: '/history', label: 'History', icon: History },
  { path: '/analytics', label: 'Analytics', icon: BarChart3 },
  { path: '/settings', label: 'Settings', icon: Settings },
];

function Layout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(true);

  return (
    <div className="min-h-screen flex bg-dark-bg">
      {/* Sidebar */}
      <aside 
        className={`${sidebarOpen ? 'w-64' : 'w-20'} 
          fixed left-0 top-0 h-full bg-dark-card/95 backdrop-blur-xl border-r border-dark-border 
          transition-all duration-300 z-50 flex flex-col`}
      >
        {/* Logo */}
        <div className="p-6 flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 
                          flex items-center justify-center shadow-lg shadow-primary-500/25">
            <Bot className="w-6 h-6 text-white" />
          </div>
          {sidebarOpen && (
            <div className="animate-fade-in">
              <h1 className="text-lg font-bold text-light">AlBot</h1>
              <p className="text-xs text-muted">Training Platform</p>
            </div>
          )}
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-3 py-4">
          <ul className="space-y-2">
            {navItems.map(({ path, label, icon: Icon }) => (
              <li key={path}>
                <NavLink
                  to={path}
                  className={({ isActive }) => `
                    flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300
                    ${isActive 
                      ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' 
                      : 'text-muted hover:text-light hover:bg-dark-border/50'
                    }
                  `}
                >
                  <Icon className="w-5 h-5 flex-shrink-0" />
                  {sidebarOpen && <span className="font-medium">{label}</span>}
                </NavLink>
              </li>
            ))}
          </ul>
        </nav>

        {/* Toggle Button */}
        <div className="p-4 border-t border-dark-border">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="w-full flex items-center justify-center gap-2 px-4 py-2 
                       text-muted hover:text-light hover:bg-dark-border/50 
                       rounded-xl transition-all duration-300"
          >
            {sidebarOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            {sidebarOpen && <span className="text-sm">Collapse</span>}
          </button>
        </div>

        {/* Version Badge */}
        {sidebarOpen && (
          <div className="p-4 border-t border-dark-border">
            <div className="flex items-center gap-2 text-xs text-muted">
              <Sparkles className="w-4 h-4 text-primary-400" />
              <span>v1.0.0</span>
            </div>
          </div>
        )}
      </aside>

      {/* Main Content */}
      <main className={`flex-1 ${sidebarOpen ? 'ml-64' : 'ml-20'} transition-all duration-300`}>
        <div className="min-h-screen p-6 lg:p-8">
          {children}
        </div>
      </main>

      {/* Background Gradient */}
      <div className="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div className="absolute top-0 left-1/4 w-96 h-96 bg-primary-500/5 rounded-full blur-3xl" />
        <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-primary-600/5 rounded-full blur-3xl" />
      </div>
    </div>
  );
}

export default Layout;
