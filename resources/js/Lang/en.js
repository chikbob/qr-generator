export default {
    flash: {
        auth: {
            login_success: 'Login successful!',
            register_success: 'Registration completed successfully!',
        },
        plan: {
            updated: 'Plan updated successfully!',
            payment_success: 'Payment successful! Plan updated.',
        },
        feedback: {
            sent: 'Your message has been sent successfully!',
        },
        qr: {
            dynamic_only_pro: 'Dynamic QR codes are only available for Pro or Enterprise.',
            saved: 'QR code saved!',
            deleted: 'QR code deleted!',
            not_found: 'QR code not found',
            deleted_all: 'All QR codes deleted successfully!',
        },
    },
    common: {
        go: 'Go',
        logout: 'Logout',
        login: 'Login',
        register: 'Register',
        unknown: 'Unknown',
    },

    header: {
        home: 'Home',
        generate: 'Generator',
        scan: 'Scanner',
        history: 'History',
        profile: 'Profile',
        plans: 'Plans',
        contacts: 'Contacts',
        admin: 'Admin',
        login: 'Login',
        register: 'Register',
        logout: 'Logout',
    },

    admin: {
        nav: {
            panel: 'Control Panel',
            dashboard: 'Dashboard',
            toSite: 'Go to site',
        },
        dashboard: {
            title: 'Admin Panel',
            subtitle: 'Administration zone. You are in system management mode.',
            topCountriesTitle: 'Top countries by scans',
            recentScansTitle: 'Recent scans',
            kpis: {
                totalScans: 'Total scans',
                scansToday: 'Scans today',
                dynamicShare: 'Dynamic QR share',
                usersToday: 'Users today',
            },
        },
        common: {
            backToTables: 'Back to tables',
            backToTable: 'Back to table',
            newRecord: 'New record',
            actions: 'Actions',
            edit: 'Edit',
            delete: 'Delete',
            noRecords: 'No records',
            confirmDelete: 'Delete this record?',
            create: 'Create',
            save: 'Save',
        },
        form: {
            createTitle: 'Create record',
            editTitle: 'Edit record',
        },
        tables: {
            users: 'Users',
            plans: 'Tariff plans',
            qr_codes: 'QR codes',
            qr_scans: 'QR scans',
            feedback: 'Feedback',
        },
    },

    home: {
        title: 'Welcome to the QR Code Generator',

        generator: {
            title: 'QR Code Generator',
            text: 'Create QR codes for text, URLs, Wi-Fi, and contacts',
        },

        scanner: {
            title: 'QR Code Scanner',
            text: 'Scan QR codes using your device camera',
        },

        history: {
            title: 'History',
            text: 'View the history of created QR codes',
        },
    },

    profile: {
        title: 'User Profile',
        name: 'Name',
        email: 'Email',
        plan: 'Plan',
        free: 'Free',
        logout: 'Logout',
    },

    auth: {
        loginTitle: 'Login',
        email: 'Email',
        password: 'Password',
        submit: 'Login',
    },

    qrAnalytics: {
        title: 'QR Code Analytics',
        content: 'Content',
        totalScans: 'Total scans',
        createdAt: 'Created at',
        activityChart: 'Activity Chart',
        scanDetails: 'Scan Details',
        date: 'Date',
        country: 'Country',
        city: 'City',
        browser: 'Browser',
        device: 'Device',
        referer: 'Source',
        scansPerDay: 'Scans per day',
    },

    qrGenerator: {
        title: 'QR Code Generator',
        placeholder: 'Enter text or URL to generate QR code',
        warning: 'Warning: Too long text may reduce QR code readability',
        characters: 'Characters',
        sizeLabel: 'QR Code Size',
        colorLabel: 'Color',
        backgroundLabel: 'Background',
        dynamicLabel: 'Make dynamic (with statistics)',
        dynamicProHint: 'Dynamic QR codes are only available for Pro or Enterprise users.',
        dynamicAlert: 'Dynamic QR codes are only available for Pro or Enterprise users.',
        downloadPNG: 'Download PNG',
        downloadSVG: 'Download SVG',
        saveHistory: 'Save to history',
        placeholderEmpty: 'Enter text above to generate QR code',
        errorGenerate: 'Error generating QR code:',
        errorGenerateSVG: 'Error generating SVG:',
        colorDark: 'Dark shade',
        colorLight: 'Light shade',

        type: {
            text: 'Text / URL',
            wifi: 'Wi-Fi',
            contact: 'Contact',
            email: 'Email',
            phone: 'Phone',
            sms: 'SMS',
            geo: 'Geolocation',
            event: 'Event',
            social: 'Social networks',
            location: "Location",
            pdf: "PDF file",
        },

        textPlaceholder: 'Enter text or URL',
        size: 'Size',
        dynamic: 'Dynamic QR',
        dynamicUnsupported: 'Dynamic QR works only for links/actions: http(s), mailto, tel, sms, geo. Use a static QR for Wi-Fi or contacts.',
        save: 'Save',
        wifi: {
            ssid: 'Network name (SSID)',
            password: 'Password',
            wpa: 'WPA / WPA2',
            wep: 'WEP',
            open: 'Open network',
        },
        contact: {
            title: 'Contact Details',
            name: 'Name',
            phone: 'Phone',
            email: 'Email',
            company: 'Company',
            website: 'Website',
            address: 'Address',
        },
        email: {
            to: 'Recipient Email',
            subject: 'Email Subject',
            body: 'Email Body',
        },
        phone: {
            number: 'Phone number',
        },

        sms: {
            number: 'Phone number',
            message: 'Message text',
        },

        geo: {
            lat: 'Latitude',
            lng: 'Longitude',
        },

        event: {
            title: 'Event title',
            start: 'Start date & time',
            end: 'End date & time',
            location: 'Location',
        },

        social: {
            telegram: 'Telegram link',
            whatsapp: 'WhatsApp link',
            instagram: 'Instagram link',
            youtube: 'YouTube link',
        },

        location: {
            mapProviders: {
                google: "Google Maps",
                yandex: "Yandex Maps",
                placeholder: "Select map"
            },
            latitude: "Latitude",
            longitude: "Longitude",
            address: "Address",
            routeButton: "Get directions"
        },

        pdf: {
            selectPlaceholder: "Select document",
            menu: "Restaurant menu",
            price: "Price list",
            instructions: "Instructions"
        },
    },

    qrHistory: {
        title: "QR Code History",
        searchPlaceholder: "🔎 Search by content...",
        filter: {
            all: "All",
            dynamic: "Dynamic only",
            static: "Static only",
        },
        sort: {
            desc: "Newest first",
            asc: "Oldest first",
        },
        deleteAll: "Delete all",
        empty: {
            title: "History is empty",
            subtitle: "Generate a QR code — it will appear here.",
        },
        type: {
            dynamic: "Dynamic QR",
            static: "Static QR",
        },
        stats: {
            views: "Views",
            visit: "Visit",
        },
        actions: {
            copy: "Copy",
            copySuccess: "Text copied! 📋",
            copyFail: "Failed to copy",
            analytics: "Analytics",
            delete: "Delete",
        },
        confirm: {
            deleteItem: "Delete this QR code?",
            deleteAll: "Delete all QR codes? This action cannot be undone.",
            deleteAllSuccess: "All QR codes successfully deleted!",
            deleteAllFail: "Failed to delete all QR codes. Please try again later.",
        },
        errors: {
            downloadError: "Error downloading QR:",
            downloadFailed: "Failed to download file 😔",
        }
    },

    qrScanner: {
        title: 'QR Code Scanner',
        startScan: 'Start Scanning',
        stopScan: 'Stop',
        loadingDevices: 'Loading devices...',
        cameraLabel: 'Select camera',
        refreshButton: 'Refresh camera list',
        scanningHint: 'Point the camera at the QR code',
        scanResultTitle: 'Scan result',
        copyButton: 'Copy',
        copySuccess: 'Text copied!',
        copyFail: 'Failed to copy',
        openButton: 'Open',
        clearButton: 'Clear',
        cameraNotFound: 'Camera not found',
        errorCameraDenied: 'Camera access denied',
        errorScanning: 'Scanning error',
        errorLoadingDevices: 'Error loading devices',
        errorOk: 'OK',
        placeholderText: 'Camera is not active. Select a camera and click "Start Scanning".',
    },

    login: {
        title: 'Login',
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Password',
        submitButton: 'Login',
    },

    register: {
        title: 'Register',
        namePlaceholder: 'Name',
        emailPlaceholder: 'Email',
        passwordPlaceholder: 'Password',
        passwordConfirmationPlaceholder: 'Confirm Password',
        submitButton: 'Register',
    },

    plans: {
        title: 'Select a Plan',
        noPlans: 'No plans available.',
        current: 'Current',
        price: 'Price',
        selected: 'Selected',
        select: 'Select',
        confirmFree: 'Are you sure you want to cancel your subscription and switch to the Free plan?',
    },

    payment: {
        title: 'Plan Payment',
        price: 'Price',
        cardNumber: 'Card Number',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        expiryDate: 'Expiry Date',
        expiryDatePlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
        processing: 'Processing...',
        pay: 'Pay',
    },

    list: {
        title: 'Feedback list',
        subject: 'Subject',
        category: 'Category',
        status: 'Status',
        createdAt: 'Created at',
        action: 'Action',
        view: 'View',
        empty: 'No requests found',
    },

    show: {
        back: '← Back to list',
        backContact: '← Back to contacts',
        category: 'Category',
        status: 'Status',
        plan: 'Plan',
        createdAt: 'Created at',
    },

    create: {
        title: 'Feedback',
        subject: 'Subject',
        category: 'Category',
        message: 'Message',
        submit: 'Send',
        submitting: 'Sending...',
    },

    categories: {
        general: 'General question',
        bug: 'Bug report',
        idea: 'Idea / Suggestion',
    },

    statuses: {
        new: 'Pending',
        in_progress: 'In progress',
        resolved: 'Resolved',
    },

    priorities: {
        high: 'Enterprise',
        medium: 'Pro',
        low: 'Free',
    },

    contacts: {
        title: 'Contacts',

        email: 'Email',
        phone: 'Phone',
        address: 'Address',
        addressValue: 'New York, Example Street, 1',
        number: '+1 (111) 999-99-99',

        feedback: {
            title: 'Leave a request',
            viewAll: 'View all requests',
        },

        form: {
            subject: 'Subject',
            subjectPlaceholder: 'Enter request subject',
            category: 'Category',
            message: 'Message',
            messagePlaceholder: 'Describe your question or issue',
            submit: 'Send request',
            success: 'Your request has been successfully sent!',
        },
    },
}
