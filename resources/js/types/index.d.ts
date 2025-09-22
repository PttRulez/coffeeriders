import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import { Role } from '@/types/enums';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    children?: NavItem[];
    href?: string;
    icon?: LucideIcon;
    isActive?: boolean;
    show: boolean;
    title: string;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    avatar?: string;
    created_at: string;
    email: string;
    email_verified_at: string | null;
    id: number;
    name: string;
    phone: string;
    role: Role;
    telegram_username: string;
    updated_at: string;
}


export type BreadcrumbItemType = BreadcrumbItem;
export * from './rent-bikes';
export * from './cycling-studio';
