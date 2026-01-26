export default {
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
        login: 'Login',
        register: 'Register',
        logout: 'Logout',
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
        },
        textPlaceholder: 'Enter text or URL',
        size: 'Size',
        dynamic: 'Dynamic QR',
        save: 'Save',
        wifi: {
            ssid: 'Network name (SSID)',
            password: 'Password',
            wpa: 'WPA / WPA2',
            wep: 'WEP',
            open: 'Open network',
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

