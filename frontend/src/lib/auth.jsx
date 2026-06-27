import { useState, useEffect, createContext, useContext } from 'react';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem('user')) || null;
    } catch {
      return null;
    }
  });
  const [token, setToken] = useState(() => localStorage.getItem('token') || null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    function onLogout() {
      setUser(null);
      setToken(null);
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
    window.addEventListener('auth:logout', onLogout);

    // Validate token on mount
    async function validate() {
      const t = localStorage.getItem('token');
      if (t) {
        try {
          const res = await fetch(`${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/me`, {
            headers: { Authorization: `Bearer ${t}` },
          });
          if (res.ok) {
            const data = await res.json();
            setUser(data.data);
            setToken(t);
          } else {
            onLogout();
          }
        } catch {
          onLogout();
        }
      }
      setLoading(false);
    }
    validate();

    return () => window.removeEventListener('auth:logout', onLogout);
  }, []);

  const login = (token, user) => {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
    setToken(token);
    setUser(user);
  };

  const logout = () => {
    fetch(`${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/logout`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
    }).catch(() => {});
    setUser(null);
    setToken(null);
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  };

  return (
    <AuthContext.Provider value={{ user, token, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
