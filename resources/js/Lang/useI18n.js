import { ref } from 'vue'

import ru from './ru'
import en from './en'
import ua from './ua'

const messages = { ru, en, ua }

// язык по умолчанию
const currentLang = ref(localStorage.getItem('lang') || 'ua')
if (typeof document !== 'undefined') {
    document.cookie = `lang=${currentLang.value}; path=/; max-age=31536000`
    document.documentElement.lang = currentLang.value
}

export function useI18n() {
    const t = (key) => {
        const parts = key.split('.')
        let value = messages[currentLang.value]

        for (const part of parts) {
            value = value?.[part]
            if (value === undefined) return key
        }

        return value
    }

    const tMaybe = (value) => {
        if (typeof value !== 'string') return value
        const translated = t(value)
        return translated === value ? value : translated
    }

    const setLang = (lang) => {
        if (!messages[lang]) return
        currentLang.value = lang
        localStorage.setItem('lang', lang)
        document.cookie = `lang=${lang}; path=/; max-age=31536000`
        document.documentElement.lang = lang
    }

    return {
        t,
        tMaybe,
        setLang,
        currentLang,
    }
}
