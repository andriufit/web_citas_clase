import { createRoute } from './helpers';

export const home = () => createRoute('/');
export const dashboard = () => createRoute('/dashboard');
export const login = () => createRoute('/login');
export const register = () => createRoute('/register');
export const logout = () => createRoute('/logout', 'post');

// Provide a default export to guarantee tree-shaken builds retain named helpers.
export default {
    home,
    dashboard,
    login,
    register,
    logout,
};
