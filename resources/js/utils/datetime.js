const UTC_PLUS_3_TZ = 'Europe/Moscow'

const dateTimeFormatter = new Intl.DateTimeFormat('ru-RU', {
    timeZone: UTC_PLUS_3_TZ,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
})

const dateFormatter = new Intl.DateTimeFormat('ru-RU', {
    timeZone: UTC_PLUS_3_TZ,
    year: 'numeric',
    month: 'long',
    day: 'numeric',
})

const dateKeyFormatter = new Intl.DateTimeFormat('en-CA', {
    timeZone: UTC_PLUS_3_TZ,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
})

export function formatDateTimeUtcPlus3(value) {
    if (!value) return '—'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '—'
    return dateTimeFormatter.format(date)
}

export function formatDateUtcPlus3(value) {
    if (!value) return '—'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '—'
    return dateFormatter.format(date)
}

export function getDateKeyUtcPlus3(value) {
    if (!value) return ''
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return ''
    return dateKeyFormatter.format(date)
}

