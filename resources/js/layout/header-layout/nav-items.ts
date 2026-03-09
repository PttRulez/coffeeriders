import type { NavItem } from '@/types';
import { BikeCategory } from '@/types/enums';
import { Bike, CalendarDays, Dumbbell, FolderKanban, Wrench } from 'lucide-vue-next';

type RouteFn = (name: string, params?: any) => string;

type GetNavItemsParams = {
    device: 'desktop' | 'mobile';
    isAdmin: boolean;
    isAdminPanel: boolean;
    isAuthenticated: boolean;
    route: RouteFn;
};

export const getNavItems = ({
    device,
    isAdmin,
    isAdminPanel,
    isAuthenticated,
    route
}: GetNavItemsParams): NavItem[] => {

    // МЕНЮ В АДМИНКЕ
    if (isAdminPanel) {
        return [
            {
                title: 'Прокат',
                show: true,
                href: '',
                children: [
                    {
                        href: route('adminka.rent-bikes.index'),
                        icon: Bike,
                        title: 'Велики',
                        show: true,
                    },
                    {
                        title: 'Бронь',
                        href: route('adminka.rent-bikes.bookings.index'),
                        icon: FolderKanban,
                        show: true,
                    },
                ],
            },
            {
                title: 'Студия',
                show: true,
                href: '',
                children: [
                    {
                        href: route('adminka.cycling-studio.index'),
                        title: 'Бронирования',
                        show: true,
                    },
                ],
            },
            {
                title: 'Мастерская',
                show: true,
                href: '',
                children: [
                    {
                        href: route('adminka.workshop-categories.index'),
                        title: 'Категории',
                        show: true,
                    },
                    {
                        href: route('adminka.workshop-services.index'),
                        title: 'Услуги',
                        show: true,
                    },
                ],
            },
            {
                title: 'Админка',
                href: route('adminka.index'),
                show: isAdmin,
            },
        ];
    }

    // ОСНОВНЫЕ ПУНКТЫ МЕНЮ
    const mainItems: NavItem[] = [
        {
            title: 'Аренда',
            href: route('rent-bikes.index'),
            icon: Bike,
            show: true,
            children: [
                {
                    title: 'Шоссер',
                    href: route('rent-bikes.category', BikeCategory.Road),
                    show: true,
                },
                {
                    title: 'Гравийные',
                    href: route('rent-bikes.category', BikeCategory.Gravel),
                    show: true,
                },
                {
                    title: 'МТБ',
                    href: route('rent-bikes.category', BikeCategory.MTB),
                    show: true,
                },
            ],
        },
        {
            title: 'Студия',
            href: route('cycling-studio.index'),
            icon: Dumbbell,
            show: true,
        },
        {
            title: 'Веломастерская',
            href: route('workshop.pricelist'),
            icon: Wrench,
            show: true,
        },
        {
            title: 'Календарь гонок',
            href: route('races.calendar'),
            icon: CalendarDays,
            show: true,
        },
    ];

    // ДРУГОЕ
    const extraItems: NavItem[] = [
        {
            title: 'Блог',
            href: route('blog.index'),
            show: true,
        },
        {
            title: 'Контакты',
            href: route('contacts'),
            show: true,
        },
        {
            title: 'Мой аккаунт',
            href: route('user-account.index'),
            show: isAuthenticated,
        },
        {
            title: 'Админка',
            href: route('adminka.index'),
            show: isAdmin,
        },
    ];

    // ПОСЛЕДНИЕ ПУНКТЫ - АВТОРИЗАЦИЯ
    const authItems: NavItem[] = [
        {
            title: 'Войти',
            href: route('login'),
            show: !isAuthenticated,
        },
        {
            title: 'Регистрация',
            href: route('register'),
            show: !isAuthenticated,
        },
    ];

    if (device === 'mobile') {
        return [...mainItems, ...extraItems, ...authItems];
    }

    return [
        ...mainItems,
        {
            title: 'Другое',
            href: '',
            show: extraItems.some((item) => item.show),
            children: extraItems,
        },
        ...authItems,
    ];
};
