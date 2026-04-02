<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .widget {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            padding: 16px;
            box-sizing: border-box;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .input,
        textarea {
            width: 100%;
            margin-top: 6px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            outline: none;
        }

        .input:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .btn {
            width: 100%;
            background: #2563eb;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .label {
            font-size: 12px;
            color: #6b7280;
        }

        .title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #000;
        }

        .msg {
            margin-top: 12px;
            font-size: 13px;
            text-align: center;
            display: none;
        }

        .msg.success {
            color: #16a34a;
        }

        .msg.error {
            color: #dc2626;
        }

        .input-error {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.15);
        }

        .error-text {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <div class="widget">
        <div class="card">
            <h2 class="title">Обратная связь</h2>
            <form id="ticket-form">
                @csrf
                <div>
                    <div class="label">Имя</div><input class="input" type="text" name="name" required>
                </div>
                <div style="display:flex; gap:10px; margin-top:12px;">
                    <div style="flex:1;">
                        <div class="label">Email</div><input class="input" type="email" name="email" required>
                    </div>
                    <div style="flex:1;">
                        <div class="label">Телефон</div><input class="input" type="text" name="phone" placeholder="+7..." required>
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <div class="label">Тема</div><input class="input" type="text" name="subject" required>
                </div>
                <div style="margin-top:12px;">
                    <div class="label">Сообщение</div><textarea class="input" name="message" rows="3" required></textarea>
                </div>
                <div style="margin-top:12px;">
                    <div class="label">Файлы</div><input type="file" name="files[]" multiple style="margin-top:6px; font-size:12px;">
                </div>
                <div style="margin-top:16px;"><button class="btn" type="submit" id="submit-btn">Отправить</button></div>
            </form>
            <div id="response-msg" class="msg"></div>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('ticket-form');
            const btn = document.getElementById('submit-btn');
            const msgBox = document.getElementById('response-msg');

            function clearErrors() {
                document.querySelectorAll('.input').forEach(el => el.classList.remove('input-error'));
                document.querySelectorAll('.error-text').forEach(el => el.remove());
            }

            function showErrors(errors) {
                Object.entries(errors).forEach(([field, messages]) => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (!input) return;
                    input.classList.add('input-error');
                    const error = document.createElement('div');
                    error.className = 'error-text';
                    error.innerText = messages[0];
                    input.parentNode.appendChild(error);
                });
            }

            form.querySelectorAll('.input').forEach(input => {
                input.addEventListener('input', () => {
                    input.classList.remove('input-error');
                    const err = input.parentNode.querySelector('.error-text');
                    if (err) err.remove();
                });
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();

                const formData = new FormData(form);

                btn.disabled = true;
                btn.innerText = 'Отправка...';
                msgBox.style.display = 'none';

                try {
                    const response = await fetch('/api/tickets/store', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();

                    if (response.ok) {
                        form.innerHTML = `<div style="text-align:center; padding:20px;">
                    <div style="font-size:18px; margin-bottom:10px;">✅</div>
                    <div style="font-weight:500;">Заявка отправлена</div>
                    <div style="font-size:13px; color:#6b7280; margin-top:5px;">Мы свяжемся с вами</div>
                </div>`;
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                status: 'success'
                            }, '*');
                        }
                    } else {
                        if (result.errors) showErrors(result.errors);
                        msgBox.innerText = result.message || 'Ошибка отправки';
                        msgBox.className = 'msg error';
                        msgBox.style.display = 'block';
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                status: 'error',
                                message: result.message
                            }, '*');
                        }
                    }
                } catch (e) {
                    msgBox.innerText = 'Ошибка сети';
                    msgBox.className = 'msg error';
                    msgBox.style.display = 'block';
                    if (window.parent !== window) {
                        window.parent.postMessage({
                            status: 'error',
                            message: 'Ошибка сети'
                        }, '*');
                    }
                }

                btn.disabled = false;
                btn.innerText = 'Отправить';
            });
        })();
    </script>

</body>

</html>