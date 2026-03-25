export default {
    flash: {
        auth: {
            login_success: 'Успішний вхід!',
            register_success: 'Реєстрація пройшла успішно!',
        },
        plan: {
            updated: 'Тариф успішно змінено!',
            payment_success: 'Оплата пройшла успішно! Тариф оновлено.',
        },
        feedback: {
            sent: 'Ваше повідомлення успішно відправлено!',
        },
        qr: {
            dynamic_only_pro: 'Динамічні QR-коди доступні лише для Pro або Enterprise.',
            saved: 'QR-код збережено!',
            deleted: 'QR-код видалено!',
            not_found: 'QR-код не знайдено',
            deleted_all: 'Усі QR-коди видалено успішно!',
        },
    },
    common: {
        go: 'Перейти',
        logout: 'Вийти',
        login: 'Увійти',
        register: 'Реєстрація',
        unknown: 'Невідомо',
    },

    header: {
        home: 'Головна',
        generate: 'Генератор',
        scan: 'Сканер',
        history: 'Історія',
        profile: 'Профіль',
        plans: 'Тарифи',
        contacts: 'Контакти',
        admin: 'Адмін',
        login: 'Увійти',
        register: 'Реєстрація',
        logout: 'Вийти',
    },

    footer: {
        brand: 'QR Code Generator',
        contacts: 'Контакти',
        plans: 'Тарифи',
        privacy: 'Конфіденційність',
    },

    admin: {
        nav: {
            panel: 'Панель керування',
            dashboard: 'Дашборд',
            toSite: 'На сайт',
        },
        dashboard: {
            title: 'Адмін панель',
            subtitle: 'Зона адміністрування. Ви перебуваєте в режимі керування системою.',
            topCountriesTitle: 'Топ країн за скануваннями',
            recentScansTitle: 'Останні сканування',
            kpis: {
                totalScans: 'Усього сканувань',
                scansToday: 'Сканувань сьогодні',
                dynamicShare: 'Частка динамічних QR',
                usersToday: 'Користувачі за день',
            },
        },
        common: {
            backToTables: 'До таблиць',
            backToTable: 'Назад до таблиці',
            newRecord: 'Новий запис',
            actions: 'Дії',
            edit: 'Редагувати',
            delete: 'Видалити',
            noRecords: 'Немає записів',
            confirmDelete: 'Видалити запис?',
            create: 'Створити',
            save: 'Зберегти',
        },
        form: {
            createTitle: 'Новий запис',
            editTitle: 'Редагування',
        },
        tables: {
            users: 'Користувачі',
            plans: 'Тарифні плани',
            qr_codes: 'QR-коди',
            qr_scans: 'Сканування QR',
            feedback: 'Звернення',
        },
    },

    home: {
        title: 'Ласкаво просимо до Генератора QR-кодів',

        generator: {
            title: 'Генератор QR-кодів',
            text: 'Створюйте QR-коди для тексту, URL, Wi-Fi та контактів',
        },

        scanner: {
            title: 'Сканер QR-кодів',
            text: 'Скануйте QR-коди за допомогою камери пристрою',
        },

        history: {
            title: 'Історія',
            text: 'Переглядайте історію створених QR-кодів',
        },
    },

    profile: {
        title: 'Профіль користувача',
        name: "Ім'я",
        email: 'Email',
        plan: 'Тариф',
        free: 'Безкоштовний',
        logout: 'Вийти',
    },

    auth: {
        loginTitle: 'Вхід',
        email: 'Email',
        password: 'Пароль',
        submit: 'Увійти',
    },

    qrAnalytics: {
        title: 'Аналітика QR-коду',
        content: 'Вміст',
        totalScans: 'Загальна кількість сканувань',
        createdAt: 'Створено',
        activityChart: 'Графік активності',
        scanDetails: 'Деталі сканувань',
        date: 'Дата',
        country: 'Країна',
        city: 'Місто',
        browser: 'Браузер',
        device: 'Пристрій',
        referer: 'Джерело',
        scansPerDay: 'Сканування за день',
        notAvailable: 'Невідомо',
        locationLocalProxy: 'Локально/Проксі',
        locationUnknown: 'Гео недоступне',
    },

    qrGenerator: {
        title: 'Генератор QR-кодів',
        placeholder: 'Введіть текст або посилання для генерації QR-коду',
        warning: 'Увага: Занадто довгий текст може знизити читаність QR-коду',
        characters: 'Символів',
        sizeLabel: 'Розмір QR-коду',
        colorLabel: 'Колір',
        backgroundLabel: 'Фон',
        dynamicLabel: 'Зробити динамічним (зі статистикою)',
        dynamicProHint: 'Динамічні QR-коди доступні лише для користувачів з планом Pro або Enterprise.',
        dynamicAlert: 'Динамічні QR-коди доступні лише для користувачів з планом Pro або Enterprise.',
        downloadPNG: 'Завантажити PNG',
        downloadSVG: 'Завантажити SVG',
        saveHistory: 'Зберегти в історію',
        placeholderEmpty: 'Введіть текст вище, щоб згенерувати QR-код',
        errorGenerate: 'Помилка генерації QR-коду:',
        errorGenerateSVG: 'Помилка генерації SVG:',
        colorDark: 'Темний відтінок',
        colorLight: 'Світлий відтінок',

        type: {
            text: 'Текст / URL',
            wifi: 'Wi-Fi',
            contact: 'Контакт',
            email: 'Email',
            phone: 'Телефон',
            sms: 'SMS',
            geo: 'Геолокація',
            event: 'Подія',
            social: 'Соціальні мережі',
            location: "Локація",
            pdf: "PDF файл",
        },

        textPlaceholder: 'Введіть текст або посилання',
        size: 'Розмір',
        dynamic: 'Динамічний QR',
        dynamicUnsupported: 'Динамічний QR працює лише для http(s), mailto, tel, sms, geo. Для Wi-Fi або контактів використовуйте статичний QR.',
        save: 'Зберегти',
        wifi: {
            ssid: 'Назва мережі (SSID)',
            password: 'Пароль',
            wpa: 'WPA / WPA2',
            wep: 'WEP',
            open: 'Відкрита мережа',
        },
        contact: {
            title: 'Контактні дані',
            name: "Ім'я",
            phone: 'Телефон',
            email: 'Email',
            company: 'Компанія',
            website: 'Сайт',
            address: 'Адреса',
        },
        email: {
            to: 'Кому (Email)',
            subject: 'Тема листа',
            body: 'Текст листа',
        },

        phone: {
            number: 'Номер телефону',
        },

        sms: {
            number: 'Номер телефону',
            message: 'Текст повідомлення',
        },

        geo: {
            lat: 'Широта',
            lng: 'Довгота',
        },

        event: {
            title: 'Назва події',
            start: 'Дата та час початку',
            end: 'Дата та час завершення',
            location: 'Місце проведення',
        },

        social: {
            telegram: 'Посилання Telegram',
            whatsapp: 'Посилання WhatsApp',
            instagram: 'Посилання Instagram',
            youtube: 'Посилання YouTube',
        },

        location: {
            mapProviders: {
                google: "Google Maps",
                yandex: "Yandex Maps",
                placeholder: "Оберіть карту"
            },
            latitude: "Широта (latitude)",
            longitude: "Довгота (longitude)",
            address: "Адреса",
            routeButton: "Прокласти маршрут"
        },

        pdf: {
            selectPlaceholder: "Виберіть документ",
            menu: "Меню ресторану",
            price: "Прайс",
            instructions: "Інструкція"
        },
    },

    qrHistory: {
        title: "Історія QR-кодів",
        searchPlaceholder: "🔎 Пошук за вмістом...",
        filter: {
            all: "Всі",
            dynamic: "Тільки динамічні",
            static: "Тільки статичні",
        },
        sort: {
            desc: "Спочатку нові",
            asc: "Спочатку старі",
        },
        deleteAll: "Видалити всі",
        empty: {
            title: "Історія порожня",
            subtitle: "Згенеруйте QR-код — і він з’явиться тут.",
        },
        type: {
            dynamic: "Динамічний QR",
            static: "Статичний QR",
        },
        stats: {
            views: "Перегляди",
            visit: "Перейти",
        },
        actions: {
            copy: "Копіювати",
            copySuccess: "Текст скопійовано! 📋",
            copyFail: "Не вдалося скопіювати",
            analytics: "Аналітика",
            delete: "Видалити",
        },
        confirm: {
            deleteItem: "Видалити цей QR-код?",
            deleteAll: "Видалити всі QR-коди? Цю дію не можна скасувати.",
            deleteAllSuccess: "Всі QR-коди успішно видалені!",
            deleteAllFail: "Не вдалося видалити всі QR-коди. Спробуйте пізніше.",
        },
        errors: {
            downloadError: "Помилка завантаження QR:",
            downloadFailed: "Не вдалося завантажити файл 😔",
        }
    },

    qrScanner: {
        title: 'Сканер QR-кодів',
        startScan: 'Почати сканування',
        stopScan: 'Зупинити',
        loadingDevices: 'Завантаження пристроїв...',
        cameraLabel: 'Оберіть камеру',
        refreshButton: 'Оновити список камер',
        scanningHint: 'Наведіть камеру на QR-код',
        scanResultTitle: 'Результат сканування',
        copyButton: 'Копіювати',
        copySuccess: 'Текст скопійовано!',
        copyFail: 'Не вдалося скопіювати',
        openButton: 'Відкрити',
        clearButton: 'Очистити',
        cameraNotFound: 'Камера не знайдена',
        errorCameraDenied: 'Доступ до камери заблоковано',
        errorScanning: 'Помилка сканування',
        errorLoadingDevices: 'Помилка завантаження пристроїв',
        errorOk: 'OK',
        placeholderText: 'Камера не активна. Оберіть камеру і натисніть "Почати сканування".',
    },

    login: {
        title: 'Вхід',
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Пароль',
        submitButton: 'Увійти',
    },

    register: {
        title: 'Реєстрація',
        namePlaceholder: "Ім'я",
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Пароль',
        passwordConfirmationPlaceholder: 'Підтвердити пароль',
        submitButton: 'Зареєструватися',
    },

    plans: {
        title: 'Вибір тарифу',
        noPlans: 'Тарифи відсутні.',
        current: 'Поточний',
        price: 'Ціна',
        selected: 'Вибрано',
        select: 'Вибрати',
        confirmFree: 'Ви дійсно хочете скасувати підписку та перейти на Free-тариф?',
    },

    payment: {
        title: 'Оплата тарифу',
        price: 'Ціна',
        cardNumber: 'Номер картки',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        expiryDate: 'Термін дії',
        expiryDatePlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
        processing: 'Оплата...',
        pay: 'Оплатити',
    },

    list: {
        title: 'Список звернень',
        subject: 'Тема',
        category: 'Категорія',
        status: 'Статус',
        createdAt: 'Дата створення',
        action: 'Дія',
        view: 'Переглянути',
        empty: 'Заявок немає',
    },

    show: {
        back: '← Назад до списку',
        backContact: '← Назад в контакти',
        category: 'Категорія',
        status: 'Статус',
        plan: 'Тариф',
        createdAt: 'Дата створення',
    },

    create: {
        title: 'Зворотній звʼязок',
        subject: 'Тема',
        category: 'Категорія',
        message: 'Повідомлення',
        submit: 'Відправити',
        submitting: 'Відправляємо...',
    },

    categories: {
        general: 'Загальне питання',
        bug: 'Помилка',
        idea: 'Ідея / Пропозиція',
    },

    statuses: {
        new: 'В очікуванні',
        in_progress: 'В роботі',
        resolved: 'Вирішено',
    },

    priorities: {
        high: 'Enterprise',
        medium: 'Pro',
        low: 'Free',
    },

    contacts: {
        title: 'Контакти',

        email: 'Пошта',
        phone: 'Телефон',
        address: 'Адреса',
        addressValue: 'м. Київ, вул. Прикладна, 1',
        number: '+380 67 123 4567',

        feedback: {
            title: 'Залишити звернення',
            viewAll: 'Переглянути всі звернення',
        },

        form: {
            subject: 'Тема',
            subjectPlaceholder: 'Введіть тему звернення',
            category: 'Категорія',
            message: 'Повідомлення',
            messagePlaceholder: 'Опишіть ваше питання або проблему',
            submit: 'Надіслати звернення',
            success: 'Ваше звернення успішно надіслано!',
        },
    },

}
