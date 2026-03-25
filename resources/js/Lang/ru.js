export default {
    flash: {
        auth: {
            login_success: 'Успешный вход!',
            register_success: 'Регистрация прошла успешно!',
        },
        plan: {
            updated: 'Тариф успешно изменен!',
            payment_success: 'Оплата прошла успешно! Тариф обновлен.',
        },
        feedback: {
            sent: 'Ваше сообщение успешно отправлено!',
        },
        qr: {
            dynamic_only_pro: 'Динамические QR-коды доступны только для Pro или Enterprise.',
            saved: 'QR-код сохранен!',
            deleted: 'QR-код удален!',
            not_found: 'QR-код не найден',
            deleted_all: 'Все QR-коды успешно удалены!',
        },
    },
    common: {
        go: 'Перейти',
        logout: 'Выйти',
        login: 'Войти',
        register: 'Регистрация',
        unknown: 'Неизвестно',
    },

    header: {
        home: 'Главная',
        generate: 'Генератор',
        scan: 'Сканер',
        history: 'История',
        profile: 'Профиль',
        plans: 'Тарифы',
        contacts: 'Контакты',
        admin: 'Админ',
        login: 'Войти',
        register: 'Регистрация',
        logout: 'Выйти',
    },

    footer: {
        brand: 'QR Code Generator',
        contacts: 'Контакты',
        plans: 'Тарифы',
        privacy: 'Конфиденциальность',
    },

    admin: {
        nav: {
            panel: 'Панель управления',
            dashboard: 'Дашборд',
            toSite: 'На сайт',
        },
        dashboard: {
            title: 'Админ панель',
            subtitle: 'Зона администрирования. Вы находитесь в режиме управления системой.',
            topCountriesTitle: 'Топ стран по сканированиям',
            recentScansTitle: 'Последние сканирования',
            kpis: {
                totalScans: 'Всего сканирований',
                scansToday: 'Сканирований сегодня',
                dynamicShare: 'Доля динамических QR',
                usersToday: 'Пользователи за день',
            },
        },
        common: {
            backToTables: 'К таблицам',
            backToTable: 'Назад к таблице',
            newRecord: 'Новая запись',
            actions: 'Действия',
            edit: 'Изменить',
            delete: 'Удалить',
            noRecords: 'Нет записей',
            confirmDelete: 'Удалить запись?',
            create: 'Создать',
            save: 'Сохранить',
        },
        form: {
            createTitle: 'Новая запись',
            editTitle: 'Редактирование',
        },
        tables: {
            users: 'Пользователи',
            plans: 'Тарифные планы',
            qr_codes: 'QR-коды',
            qr_scans: 'Сканирования QR',
            feedback: 'Обращения',
        },
    },

    home: {
        title: 'Добро пожаловать в Генератор QR-кодов',

        generator: {
            title: 'Генератор QR-кодов',
            text: 'Создавайте QR-коды для текста, URL, Wi-Fi и контактов',
        },

        scanner: {
            title: 'Сканер QR-кодов',
            text: 'Сканируйте QR-коды с помощью камеры устройства',
        },

        history: {
            title: 'История',
            text: 'Просматривайте историю созданных QR-кодов',
        },
    },

    profile: {
        title: 'Профиль пользователя',
        name: 'Имя',
        email: 'Email',
        plan: 'Тариф',
        free: 'Бесплатный',
        logout: 'Выйти',
    },

    auth: {
        loginTitle: 'Вход',
        email: 'Email',
        password: 'Пароль',
        submit: 'Войти',
    },

    qrAnalytics: {
        title: 'Аналитика QR-кода',
        content: 'Содержимое',
        totalScans: 'Общее количество сканирований',
        createdAt: 'Создано',
        activityChart: 'График активности',
        scanDetails: 'Детали сканирований',
        date: 'Дата',
        country: 'Страна',
        city: 'Город',
        browser: 'Браузер',
        device: 'Устройство',
        referer: 'Источник',
        scansPerDay: 'Сканирования за день',
        notAvailable: 'Неизвестно',
        locationLocalProxy: 'Локально/Прокси',
        locationUnknown: 'Гео недоступно',
    },

    qrGenerator: {
        title: 'Генератор QR-кодов',
        placeholder: 'Введите текст или ссылку для генерации QR-кода',
        warning: 'Внимание: Слишком длинный текст может снизить читаемость QR-кода',
        characters: 'Символов',
        sizeLabel: 'Размер QR-кода',
        colorLabel: 'Цвет',
        backgroundLabel: 'Фон',
        dynamicLabel: 'Сделать динамическим (со статистикой)',
        dynamicProHint: 'Динамические QR-коды доступны только для пользователей с тарифом Pro или Enterprise.',
        dynamicAlert: 'Динамические QR-коды доступны только для пользователей с тарифом Pro или Enterprise.',
        downloadPNG: 'Скачать PNG',
        downloadSVG: 'Скачать SVG',
        saveHistory: 'Сохранить в историю',
        placeholderEmpty: 'Введите текст выше, чтобы сгенерировать QR-код',
        errorGenerate: 'Ошибка генерации QR-кода:',
        errorGenerateSVG: 'Ошибка генерации SVG:',
        colorDark: 'Темный оттенок',
        colorLight: 'Светлый оттенок',

        type: {
            text: 'Текст / URL',
            wifi: 'Wi-Fi',
            contact: 'Контакт',
            email: 'Email',
            phone: 'Телефон',
            sms: 'SMS',
            geo: 'Геолокация',
            event: 'Событие',
            social: 'Социальные сети',
            location: "Локация",
            pdf: "PDF файл"
        },

        textPlaceholder: 'Введите текст или ссылку',
        size: 'Размер',
        dynamic: 'Динамический QR',
        dynamicUnsupported: 'Динамический QR работает только для http(s), mailto, tel, sms, geo. Для Wi-Fi или контактов используйте статический QR.',
        save: 'Сохранить',
        wifi: {
            ssid: 'Название сети (SSID)',
            password: 'Пароль',
            wpa: 'WPA / WPA2',
            wep: 'WEP',
            open: 'Открытая сеть',
        },
        contact: {
            title: 'Контактные данные',
            name: 'Имя',
            phone: 'Телефон',
            email: 'Email',
            company: 'Компания',
            website: 'Сайт',
            address: 'Адрес',
        },
        email: {
            to: 'Кому (Email)',
            subject: 'Тема письма',
            body: 'Текст письма',
        },

        phone: {
            number: 'Номер телефона',
        },

        sms: {
            number: 'Номер телефона',
            message: 'Текст сообщения',
        },

        geo: {
            lat: 'Широта',
            lng: 'Долгота',
        },

        event: {
            title: 'Название события',
            start: 'Дата и время начала',
            end: 'Дата и время окончания',
            location: 'Место проведения',
        },

        social: {
            telegram: 'Telegram ссылка',
            whatsapp: 'WhatsApp ссылка',
            instagram: 'Instagram ссылка',
            youtube: 'YouTube ссылка',
        },

        location: {
            mapProviders: {
                google: "Google Maps",
                yandex: "Yandex Maps",
                placeholder: "Выберите карту"
            },
            latitude: "Широта (latitude)",
            longitude: "Долгота (longitude)",
            address: "Адрес",
            routeButton: "Проложить маршрут"
        },

        pdf: {
            selectPlaceholder: "Выберите документ",
            menu: "Меню ресторана",
            price: "Прайс",
            instructions: "Инструкция"
        },
    },

    qrHistory: {
        title: "История QR-кодов",
        searchPlaceholder: "🔎 Поиск по содержимому...",
        filter: {
            all: "Все",
            dynamic: "Только динамические",
            static: "Только статические",
        },
        sort: {
            desc: "Сначала новые",
            asc: "Сначала старые",
        },
        deleteAll: "Удалить все",
        empty: {
            title: "История пуста",
            subtitle: "Сгенерируйте QR-код — и он появится здесь.",
        },
        type: {
            dynamic: "Динамический QR",
            static: "Статический QR",
        },
        stats: {
            views: "Просмотры",
            visit: "Перейти",
        },
        actions: {
            copy: "Копировать",
            copySuccess: "Текст скопирован! 📋",
            copyFail: "Не удалось скопировать",
            analytics: "Аналитика",
            delete: "Удалить",
        },
        confirm: {
            deleteItem: "Удалить этот QR-код?",
            deleteAll: "Удалить все QR-коды? Это действие нельзя отменить.",
            deleteAllSuccess: "Все QR-коды успешно удалены!",
            deleteAllFail: "Не удалось удалить все QR-коды. Попробуйте позже.",
        },
        errors: {
            downloadError: "Ошибка загрузки QR:",
            downloadFailed: "Не удалось загрузить файл 😔",
        }
    },

    qrScanner: {
        title: 'Сканер QR-кодов',
        startScan: 'Начать сканирование',
        stopScan: 'Остановить',
        loadingDevices: 'Загрузка устройств...',
        cameraLabel: 'Выберите камеру',
        refreshButton: 'Обновить список камер',
        scanningHint: 'Наведите камеру на QR-код',
        scanResultTitle: 'Результат сканирования',
        copyButton: 'Копировать',
        copySuccess: 'Текст скопирован!',
        copyFail: 'Не удалось скопировать',
        openButton: 'Открыть',
        clearButton: 'Очистить',
        cameraNotFound: 'Камера не найдена',
        errorCameraDenied: 'Доступ к камере заблокирован',
        errorScanning: 'Ошибка сканирования',
        errorLoadingDevices: 'Ошибка загрузки устройств',
        errorOk: 'OK',
        placeholderText: 'Камера не активна. Выберите камеру и нажмите "Начать сканирование".',
    },

    login: {
        title: 'Вход',
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Пароль',
        submitButton: 'Войти',
    },

    register: {
        title: 'Регистрация',
        namePlaceholder: 'Имя',
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Пароль',
        passwordConfirmationPlaceholder: 'Подтвердить пароль',
        submitButton: 'Зарегистрироваться',
    },

    plans: {
        title: 'Выбор тарифа',
        noPlans: 'Тарифы отсутствуют.',
        current: 'Текущий',
        price: 'Цена',
        selected: 'Выбрано',
        select: 'Выбрать',
        confirmFree: 'Вы действительно хотите отменить подписку и перейти на бесплатный тариф?',
    },

    payment: {
        title: 'Оплата тарифа',
        price: 'Цена',
        cardNumber: 'Номер карты',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        expiryDate: 'Срок действия',
        expiryDatePlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
        processing: 'Оплата...',
        pay: 'Оплатить',
    },

    list: {
        title: 'Список обращений',
        subject: 'Тема',
        category: 'Категория',
        status: 'Статус',
        createdAt: 'Дата создания',
        action: 'Действие',
        view: 'Просмотреть',
        empty: 'Заявок нет',
    },

    show: {
        back: '← Назад к списку',
        backContact: '← Назад в контакты',
        category: 'Категория',
        status: 'Статус',
        plan: 'Тариф',
        createdAt: 'Дата создания',
    },

    create: {
        title: 'Обратная связь',
        subject: 'Тема',
        category: 'Категория',
        message: 'Сообщение',
        submit: 'Отправить',
        submitting: 'Отправляем...',
    },

    categories: {
        general: 'Общий вопрос',
        bug: 'Ошибка',
        idea: 'Идея / Предложение',
    },

    statuses: {
        new: 'В ожидании',
        in_progress: 'В работе',
        resolved: 'Решено',
    },

    priorities: {
        high: 'Enterprise',
        medium: 'Pro',
        low: 'Free',
    },

    contacts: {
        title: 'Контакты',

        email: 'Почта',
        phone: 'Телефон',
        address: 'Адрес',
        addressValue: 'г. Мариуполь, ул. Примерная, 1',
        number: '+7 (949) 999-99-99',

        feedback: {
            title: 'Оставить обращение',
            viewAll: 'Посмотреть все обращения',
        },

        form: {
            subject: 'Тема',
            subjectPlaceholder: 'Введите тему обращения',
            category: 'Категория',
            message: 'Сообщение',
            messagePlaceholder: 'Опишите ваш вопрос или проблему',
            submit: 'Отправить обращение',
            success: 'Ваше обращение успешно отправлено!',
        },
    },

}
