import { DateValue } from 'reka-ui';
import { parse, format } from "date-fns"

export function dateValueToIso(val: DateValue): string {
  return `${val.year}-${String(val.month).padStart(2, '0')}-${String(val.day).padStart(2, '0')}`;
}

export function dateTimeToTime(datetime: string): string {
    const d = parse(datetime, "yyyy-MM-dd HH:mm:ss", new Date())
    return format(d, "HH:mm")
}