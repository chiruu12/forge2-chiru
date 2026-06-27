const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000';

function getToken() {
  return localStorage.getItem('token');
}

export async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`;
  const headers = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
    ...options.headers,
  };

  const token = getToken();
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const res = await fetch(url, {
    ...options,
    headers,
  });

  if (res.status === 401) {
    localStorage.removeItem('token');
    window.dispatchEvent(new Event('auth:logout'));
  }

  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.message || `HTTP ${res.status}`);
  }

  if (res.status === 204) return null;
  return res.json();
}

export async function get(path, params) {
  const qs = params ? '?' + new URLSearchParams(params).toString() : '';
  return apiFetch(path + qs, { method: 'GET' });
}

export async function post(path, body) {
  return apiFetch(path, {
    method: 'POST',
    body: JSON.stringify(body),
  });
}

export async function put(path, body) {
  return apiFetch(path, {
    method: 'PUT',
    body: JSON.stringify(body),
  });
}

export async function del(path) {
  return apiFetch(path, { method: 'DELETE' });
}
