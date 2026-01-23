import { ref } from 'vue'

import ru from './ru'
import en from './en'
import ua from './ua'

const messages = { ru, en, ua }

// язык по умолчанию
const currentLang = ref(localStorage.getItem('lang') || 'ru')

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

    const setLang = (lang) => {
        if (!messages[lang]) return
        currentLang.value = lang
        localStorage.setItem('lang', lang)
    }

    return {
        t,
        setLang,
        currentLang,
    }
}
