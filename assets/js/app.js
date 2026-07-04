(function () {
    const storageKey = 'developer-ruhban-theme';
    const sidebarStateKey = 'developer-ruhban-sidebar-open';
    const html = document.documentElement;
    const body = document.body;
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const readingProgress = document.querySelector('[data-reading-progress]');
    const searchForms = document.querySelectorAll('[data-search-form]');
    const richEditors = document.querySelectorAll('.rich-editor');
    const dirtyForms = document.querySelectorAll('[data-dirty-form]');
    const dirtyStateNode = document.querySelector('[data-dirty-state]');
    const commandPalette = document.querySelector('[data-command-palette]');
    const commandPaletteOpeners = document.querySelectorAll('[data-command-palette-open]');
    const commandPaletteClosers = document.querySelectorAll('[data-command-palette-close]');
    const commandPaletteInput = document.querySelector('[data-command-palette-input]');
    const commandItems = commandPalette ? Array.from(commandPalette.querySelectorAll('[data-command-item]')) : [];

    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const storedTheme = window.localStorage.getItem(storageKey);
    const initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

    const applyTheme = (value) => {
        html.setAttribute('data-theme', value);
        window.localStorage.setItem(storageKey, value);
        document.cookie = `theme=${value}; path=/; max-age=31536000; samesite=lax`;
    };

    const setDirtyState = (isDirty) => {
        if (!dirtyStateNode) {
            return;
        }

        dirtyStateNode.textContent = isDirty ? 'Unsaved changes' : 'Ready to publish';
        dirtyStateNode.classList.toggle('is-dirty', isDirty);
        dirtyStateNode.classList.toggle('is-saved', !isDirty);
    };

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

    const renderSuggestions = (items, input, container) => {
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
                if (input.form) {
                    input.form.submit();
                }
            });
            container.appendChild(button);
        });

        container.hidden = false;
    };

    const renderSearchResults = (resultsContainer, summaryNode, results, total) => {
        if (!resultsContainer) {
            return;
        }

        if (summaryNode) {
            summaryNode.textContent = `${typeof total === 'number' ? total : results.length} results`;
        }

        if (!results.length) {
            resultsContainer.innerHTML = `
                <article class="card-surface empty-state">
                    <h2>No results yet</h2>
                    <p>Try a different keyword, remove a filter, or use the discovery panels below.</p>
                </article>
            `;
            return;
        }

        resultsContainer.innerHTML = results.map((post) => `
            <article class="post-card card-surface">
                <p class="post-card__meta">${escapeHtml(post.content_type_name || '')}</p>
                <h2><a href="${escapeHtml(post.url || '#')}">${escapeHtml(post.title || '')}</a></h2>
                <p>${escapeHtml(post.excerpt || 'Open the content page for more context.')}</p>
                <div class="post-card__footer">
                    <span>${escapeHtml(post.author_name || '')}</span>
                    <span>${escapeHtml(String(post.view_count || 0))} views</span>
                </div>
            </article>
        `).join('');
    };

    const openCommandPalette = () => {
        if (!commandPalette) {
            return;
        }

        commandPalette.hidden = false;
        body.classList.add('palette-open');

        if (commandPaletteInput) {
            commandPaletteInput.value = '';
            commandPaletteInput.focus();
        }

        commandItems.forEach((item) => {
            item.hidden = false;
            item.classList.remove('is-active');
        });
    };

    const closeCommandPalette = () => {
        if (!commandPalette) {
            return;
        }

        commandPalette.hidden = true;
        body.classList.remove('palette-open');
    };

    applyTheme(initialTheme);

    if (window.localStorage.getItem(sidebarStateKey) === '1') {
        body.classList.add('sidebar-open');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-open');
            window.localStorage.setItem(sidebarStateKey, body.classList.contains('sidebar-open') ? '1' : '0');
        });
    }

    dirtyForms.forEach((form) => {
        let isDirty = false;
        const markDirty = () => {
            if (!isDirty) {
                isDirty = true;
                setDirtyState(true);
            }
        };

        form.addEventListener('input', markDirty);
        form.addEventListener('change', markDirty);
        form.addEventListener('submit', () => {
            isDirty = false;
            setDirtyState(false);
        });
    });

    setDirtyState(false);

    commandPaletteOpeners.forEach((button) => {
        button.addEventListener('click', openCommandPalette);
    });

    commandPaletteClosers.forEach((button) => {
        button.addEventListener('click', closeCommandPalette);
    });

    if (commandPalette && commandPaletteInput) {
        const filterCommandItems = () => {
            const term = commandPaletteInput.value.trim().toLowerCase();
            let firstVisible = null;

            commandItems.forEach((item) => {
                const haystack = `${item.textContent || ''} ${(item.getAttribute('data-keywords') || '')}`.toLowerCase();
                const visible = term === '' || haystack.includes(term);
                item.hidden = !visible;

                if (visible && !firstVisible) {
                    firstVisible = item;
                }
            });

            commandItems.forEach((item) => item.classList.remove('is-active'));
            if (firstVisible) {
                firstVisible.classList.add('is-active');
            }
        };

        commandPaletteInput.addEventListener('input', filterCommandItems);

        commandItems.forEach((item) => {
            item.addEventListener('mouseenter', () => {
                commandItems.forEach((node) => node.classList.remove('is-active'));
                item.classList.add('is-active');
            });

            item.addEventListener('click', () => {
                closeCommandPalette();
            });
        });

        commandPalette.addEventListener('click', (event) => {
            if (event.target === commandPalette) {
                closeCommandPalette();
            }
        });
    }

    if (window.ClassicEditor && richEditors.length) {
        Array.prototype.forEach.call(richEditors, (element) => {
            window.ClassicEditor.create(element, {
                toolbar: [
                    'heading', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            }).catch(() => {});
        });
    }

    searchForms.forEach((form) => {
        const input = form.querySelector('[data-search-input]');
        if (!input) {
            return;
        }

        let suggestionContainer = form.querySelector('[data-search-suggestions]');
        if (!suggestionContainer) {
            suggestionContainer = document.createElement('div');
            suggestionContainer.className = 'search-suggestions';
            suggestionContainer.hidden = true;
            input.insertAdjacentElement('afterend', suggestionContainer);
        }

        const suggestEndpoint = form.getAttribute('data-search-suggest-endpoint');
        const instantEndpoint = form.getAttribute('data-search-instant-endpoint');
        const resultsContainer = document.querySelector('[data-search-results]');
        const summaryNode = document.querySelector('[data-search-summary]');
        let focused = false;

        const requestSuggestions = debounce(async () => {
            const term = input.value.trim();

            if (!suggestEndpoint || !focused || term.length < 2) {
                suggestionContainer.hidden = true;
                return;
            }

            try {
                const response = await window.fetch(`${suggestEndpoint}?term=${encodeURIComponent(term)}`, {
                    headers: { Accept: 'application/json' }
                });
                const payload = await response.json();
                renderSuggestions(payload.suggestions || [], input, suggestionContainer);
            } catch (error) {
                suggestionContainer.hidden = true;
            }
        }, 180);

        const requestInstantResults = debounce(async () => {
            const term = input.value.trim();

            if (!instantEndpoint || !resultsContainer) {
                return;
            }

            try {
                const params = new window.URLSearchParams(new window.FormData(form));
                params.set('q', term);
                const response = await window.fetch(`${instantEndpoint}?${params.toString()}`, {
                    headers: { Accept: 'application/json' }
                });
                const payload = await response.json();
                renderSearchResults(resultsContainer, summaryNode, payload.results || [], payload.pagination && typeof payload.pagination.total !== 'undefined' ? payload.pagination.total : null);
            } catch (error) {
                // Keep server-rendered content if live search fails.
            }
        }, 220);

        input.addEventListener('input', () => {
            requestSuggestions();
            requestInstantResults();
        });

        input.addEventListener('focus', () => {
            focused = true;
            if (input.value.trim().length >= 2) {
                requestSuggestions();
            }
        });

        input.addEventListener('blur', () => {
            window.setTimeout(() => {
                focused = false;
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

    const copyButton = document.querySelector('[data-copy-link]');
    if (copyButton) {
        copyButton.addEventListener('click', async () => {
            const copyUrl = copyButton.getAttribute('data-copy-url') || window.location.href;
            try {
                if (window.navigator.clipboard && window.isSecureContext) {
                    await window.navigator.clipboard.writeText(copyUrl);
                } else {
                    const tempInput = document.createElement('input');
                    tempInput.value = copyUrl;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                }

                const label = copyButton.textContent;
                copyButton.textContent = 'Copied';
                window.setTimeout(() => {
                    copyButton.textContent = label;
                }, 1200);
            } catch (error) {
                copyButton.textContent = 'Copy failed';
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && commandPalette && !commandPalette.hidden) {
            event.preventDefault();
            closeCommandPalette();
            return;
        }

        if (event.ctrlKey && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openCommandPalette();
            return;
        }

        if ((event.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName || '')) && document.querySelector('[data-search-input]')) {
            event.preventDefault();
            document.querySelector('[data-search-input]').focus();
        }
    });
})();