(function () {
    const storageKey = 'developer-ruhban-theme';
    const html = document.documentElement;
    const toggle = document.querySelector('[data-theme-toggle]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarStateKey = 'developer-ruhban-sidebar-open';
    const richEditors = document.querySelectorAll('.rich-editor');
    const searchForms = document.querySelectorAll('[data-search-form]');

    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const storedTheme = window.localStorage.getItem(storageKey);
    const theme = storedTheme || (systemPrefersDark ? 'dark' : 'light');

    const applyTheme = (value) => {
        html.setAttribute('data-theme', value);
        window.localStorage.setItem(storageKey, value);
        document.cookie = `theme=${value}; path=/; max-age=31536000; samesite=lax`;
    };

    applyTheme(theme);

    if (window.localStorage.getItem(sidebarStateKey) === '1') {
        document.body.classList.add('sidebar-open');
    }

    if (toggle) {
        toggle.addEventListener('click', () => {
            const nextTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
            window.localStorage.setItem(sidebarStateKey, document.body.classList.contains('sidebar-open') ? '1' : '0');
        });
    }

    if (window.ClassicEditor && richEditors.length) {
        Array.prototype.forEach.call(richEditors, (element) => {
            window.ClassicEditor.create(element, {
                toolbar: [
                    'heading', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'
                ]
            }).catch(() => {});
        });
    }

    const readingProgress = document.querySelector('[data-reading-progress]');

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const debounce = (callback, delay) => {
        let timer = null;

        return (...args) => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback(...args), delay);
        };
    };

    const renderPostCard = (post) => `
        <article class="post-card card-surface">
            <p class="post-card__meta">${escapeHtml(post.content_type_name || '')}</p>
            <h2><a href="${escapeHtml(post.url || '#')}">${escapeHtml(post.title || '')}</a></h2>
            <p>${escapeHtml(post.excerpt || 'Open the content page for more context.')}</p>
            <div class="post-card__footer">
                <span>${escapeHtml(post.author_name || '')}</span>
                <span>${escapeHtml(String(post.view_count || 0))} views</span>
            </div>
        </article>
    `;

    const renderSuggestionList = (items, input, container) => {
        container.innerHTML = '';

        if (!items.length) {
            container.hidden = true;
            return;
        }

        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'search-suggestions__item';
            button.innerHTML = `<strong>${escapeHtml(item.label || '')}</strong>${item.meta ? `<span>${escapeHtml(item.meta)}</span>` : ''}`;
            button.addEventListener('click', () => {
                if (item.kind && item.kind !== 'query' && item.href) {
                    window.location.href = item.href;
                    return;
                }

                input.value = item.value || item.label || '';
                container.hidden = true;
                input.form?.submit();
            });
            container.appendChild(button);
        });

        container.hidden = false;
    };

    const renderSearchResults = (resultsContainer, summaryNode, results, total = null) => {
        if (summaryNode) {
            summaryNode.textContent = `${total !== null ? total : results.length} results`;
        }

        resultsContainer.innerHTML = results.length ? results.map(renderPostCard).join('') : `
            <article class="card-surface empty-state">
                <h2>No results yet</h2>
                <p>Try a different keyword, remove a filter, or use the discovery panels below.</p>
            </article>
        `;
    };

    searchForms.forEach((form) => {
        const input = form.querySelector('[data-search-input]');
        if (!input) {
            return;
        }

        const suggestionEndpoint = form.getAttribute('data-search-suggest-endpoint');
        const instantEndpoint = form.getAttribute('data-search-instant-endpoint');
        let suggestionContainer = form.querySelector('[data-search-suggestions]');

        if (!suggestionContainer) {
            suggestionContainer = document.createElement('div');
            suggestionContainer.className = 'search-suggestions';
            suggestionContainer.hidden = true;
            input.insertAdjacentElement('afterend', suggestionContainer);
        }

        let isFocused = false;

        const resultsContainer = document.querySelector('[data-search-results]');
        const summaryNode = document.querySelector('[data-search-summary]');

        const requestSuggestions = debounce(async () => {
            const term = input.value.trim();
            if (!suggestionEndpoint || term.length < 2 || !isFocused) {
                suggestionContainer.hidden = true;
                return;
            }

            try {
                const response = await window.fetch(`${suggestionEndpoint}?term=${encodeURIComponent(term)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await response.json();
                renderSuggestionList(payload.suggestions || [], input, suggestionContainer);
            } catch (error) {
                suggestionContainer.hidden = true;
            }
        }, 200);

        const requestInstantResults = debounce(async () => {
            const term = input.value.trim();

            if (!instantEndpoint || !resultsContainer) {
                return;
            }

            try {
                const params = new window.URLSearchParams(new window.FormData(form));
                params.set('q', term);
                const response = await window.fetch(`${instantEndpoint}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const payload = await response.json();
                renderSearchResults(resultsContainer, summaryNode, payload.results || [], payload.pagination && typeof payload.pagination.total !== 'undefined' ? payload.pagination.total : null);
            } catch (error) {
                // Keep the server-rendered results if instant search fails.
            }
        }, 250);

        input.addEventListener('input', () => {
            requestSuggestions();
            requestInstantResults();
        });

        input.addEventListener('focus', () => {
            isFocused = true;
            if (input.value.trim().length >= 2) {
                requestSuggestions();
            }
        });

        input.addEventListener('blur', () => {
            window.setTimeout(() => {
                isFocused = false;
                suggestionContainer.hidden = true;
            }, 120);
        });

        document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) {
                suggestionContainer.hidden = true;
            }
        });
    });

    if (readingProgress) {
        const updateReadingProgress = () => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = totalHeight > 0 ? Math.max(0, Math.min(1, scrollTop / totalHeight)) : 0;

            readingProgress.style.setProperty('--reading-progress', `${Math.round(progress * 100)}%`);
        };

        window.addEventListener('scroll', updateReadingProgress, { passive: true });
        window.addEventListener('resize', updateReadingProgress);
        updateReadingProgress();
    }
})();

