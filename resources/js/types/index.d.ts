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
    href: string;
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
    role: Role;
    updated_at: string;
}


export type BreadcrumbItemType = BreadcrumbItem;
