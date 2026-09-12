(() => {
  'use strict';
  const form = document.querySelector('[data-guide-provider-search]');
  const dataNode = document.querySelector('[data-guide-provider-data]');
  const results = document.querySelector('[data-guide-provider-results]');
  if (!form || !dataNode || !results) return;

  let providers = [];
  try {
    providers = JSON.parse(dataNode.textContent || '[]').filter((item) =>
      item && typeof item.name === 'string' && /^https:\/\/guide\.cloudin\.asia\/provider\/[a-zA-Z0-9._%-]+$/.test(String(item.url || ''))
    );
  } catch (_) {
    providers = [];
  }

  const input = form.querySelector('input[type="search"]');
  const normalize = (value) => String(value || '').trim().toLocaleLowerCase();
  const track = (mode, query) => {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({event: 'homepage_guide_search', search_mode: mode, search_term: query});
  };
  const render = (query) => {
    results.replaceChildren();
    const needle = normalize(query);
    if (!needle) {
      results.hidden = true;
      return [];
    }
    const matches = providers
      .filter((provider) => normalize(provider.name).includes(needle))
      .sort((a, b) => Number(!normalize(a.name).startsWith(needle)) - Number(!normalize(b.name).startsWith(needle)) || a.name.localeCompare(b.name))
      .slice(0, 6);
    matches.forEach((provider) => {
      const item = document.createElement('li');
      const link = document.createElement('a');
      link.href = provider.url;
      link.rel = 'external';
      link.textContent = provider.name;
      link.addEventListener('click', () => track('suggestion', provider.name));
      item.append(link);
      results.append(item);
    });
    results.hidden = matches.length === 0;
    return matches;
  };

  input.addEventListener('input', () => render(input.value));
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      results.hidden = true;
      input.setAttribute('aria-expanded', 'false');
    }
  });
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const query = input.value.trim();
    const exact = providers.find((provider) => normalize(provider.name) === normalize(query));
    if (exact) {
      track('exact-provider', exact.name);
      window.location.assign(exact.url);
      return;
    }
    const matches = render(query);
    if (matches.length) {
      results.querySelector('a')?.focus();
      track('suggestions', query);
      return;
    }
    track('comparison-fallback', query);
    window.location.assign('https://guide.cloudin.asia/arena');
  });
})();
