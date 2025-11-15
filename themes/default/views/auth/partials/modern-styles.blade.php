@push('styles')
    <style>
        :root {
            --auth-bg: #05060f;
            --auth-card-bg: rgba(6, 8, 24, 0.92);
            --auth-card-border: rgba(148, 163, 184, 0.2);
            --auth-primary: #6366f1;
            --auth-primary-dark: #4f46e5;
            --auth-text: #f8fafc;
            --auth-muted: rgba(226, 232, 240, 0.75);
        }

        .auth-modern-body {
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at top, rgba(99, 102, 241, 0.4), rgba(5, 6, 15, 0.98));
            font-family: 'Inter', 'Nunito', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--auth-text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }

        .auth-container {
            width: 100%;
            max-width: 1080px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .auth-intro {
            padding: 2rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(148, 163, 184, 0.15);
            background: rgba(6, 8, 24, 0.4);
            box-shadow: 0 30px 60px rgba(2, 6, 23, 0.35);
        }

        .auth-intro h1 {
            font-size: clamp(2rem, 3vw, 2.8rem);
            margin-bottom: 1rem;
        }

        .auth-intro p {
            color: var(--auth-muted);
            margin-bottom: 1rem;
        }

        .auth-intro ul {
            list-style: none;
            padding: 0;
            margin: 1rem 0 0;
            display: grid;
            gap: 0.75rem;
            color: var(--auth-muted);
        }

        .auth-intro ul li::before {
            content: '▢';
            color: var(--auth-primary);
            margin-right: 0.75rem;
        }

        .auth-card {
            background: var(--auth-card-bg);
            border: 1px solid var(--auth-card-border);
            border-radius: 1.5rem;
            padding: 2.25rem;
            box-shadow: 0 40px 80px rgba(2, 6, 23, 0.6);
            position: relative;
        }

        .auth-brand {
            text-transform: uppercase;
            letter-spacing: 0.35em;
            font-size: 0.75rem;
            color: var(--auth-muted);
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            margin-bottom: 1.5rem;
        }

        .auth-brand strong {
            letter-spacing: 0.1em;
            font-size: 1.25rem;
            color: var(--auth-text);
        }

        .auth-title {
            font-size: 1.9rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .auth-subtitle {
            color: var(--auth-muted);
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .auth-field {
            margin-bottom: 1.25rem;
        }

        .auth-field label {
            display: block;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.7);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .auth-input {
            width: 100%;
            background: rgba(5, 8, 24, 0.8);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 0.55rem;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            color: var(--auth-text);
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .auth-input:focus {
            outline: none;
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.35);
        }

        .auth-input::placeholder {
            color: rgba(226, 232, 240, 0.5);
        }

        .auth-error {
            margin-top: 0.35rem;
            font-size: 0.85rem;
            color: #fca5a5;
        }

        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .auth-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: var(--auth-muted);
        }

        .auth-checkbox input {
            accent-color: var(--auth-primary);
            width: 16px;
            height: 16px;
        }

        .auth-btn {
            background: var(--auth-primary);
            color: #fff;
            border: none;
            border-radius: 2px;
            padding: 0.95rem 1.6rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            flex-shrink: 0;
        }

        .auth-btn:hover {
            background: var(--auth-primary-dark);
            box-shadow: 0 20px 35px rgba(99, 102, 241, 0.35);
            transform: translateY(-1px);
        }

        .auth-links {
            margin-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .auth-links a {
            color: #c4d1ff;
        }

        .auth-links a:hover {
            color: #fff;
        }

        .auth-legal {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.85rem;
            color: rgba(226, 232, 240, 0.6);
        }

        .auth-legal a {
            color: #fff;
        }

        .auth-alert {
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            color: var(--auth-text);
            margin-bottom: 1.5rem;
        }

        @media (max-width: 900px) {
            .auth-modern-body {
                padding: 2.5rem 1.5rem;
            }
        }

        @media (max-width: 640px) {
            .auth-modern-body {
                padding: 2rem 1rem;
            }

            .auth-card,
            .auth-intro {
                padding: 1.75rem;
            }
        }
    </style>
@endpush

