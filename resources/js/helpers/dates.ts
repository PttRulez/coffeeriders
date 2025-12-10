import { CalendarDate } from '@internationalized/date';
import { format, parse } from 'date-fns';
import { ru } from 'date-fns/locale';
import { DateValue } from 'reka-ui';

export function dateValueToIso(val: DateValue): string {
    return `${val.year}-${String(val.month).padStart(2, '0')}-${String(val.day).padStart(2, '0')}`;
}

export function dateTimeToTime(datetime: string, f = 'HH:mm'): string {
    const d = parse(datetime, 'yyyy-MM-dd HH:mm', new Date());

    return format(d, f);
}

export function dateTimePrettify(datetime: string): string {
    const d = parse(datetime, 'yyyy-MM-dd HH:mm', new Date());

    return format(d, 'd.MM - HH:mm', { locale: ru });
}

export function dateTimeToDateValue(datetime: string): DateValue {
    const datePart = datetime.split(' ')[0];

    const [year, month, day] = datePart.split('-').map(Number);
    return new CalendarDate(year, month, day);
}
