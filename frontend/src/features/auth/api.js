import { post, get } from '../../lib/api';

export function login(credentials) {
  return post('/api/login', credentials);
}

export function register(data) {
  return post('/api/register', data);
}

export function me() {
  return get('/api/me');
}

export function logout() {
  return post('/api/logout');
}
