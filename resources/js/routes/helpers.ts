export type RouteMethod = 'get' | 'post' | 'put' | 'patch' | 'delete';

export interface RouteDescriptor {
    url: string;
    method: RouteMethod;
    form: () => { action: string; method: RouteMethod };
    toString: () => string;
    valueOf: () => string;
}

export const createRoute = (
    path: string,
    method: RouteMethod = 'get',
): RouteDescriptor => ({
    url: path,
    method,
    form: () => ({ action: path, method }),
    toString: () => path,
    valueOf: () => path,
});
