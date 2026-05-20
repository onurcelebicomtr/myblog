document.addEventListener('DOMContentLoaded', () => {
  loadSettings();
  if (document.getElementById('posts-grid')) loadPosts();
});

function loadSettings() {
  fetch('api/settings.php')
    .then(r => r.json())
    .then(s => {
      const t = document.getElementById('page-title');
      if (t && !t.textContent.includes('-')) t.textContent = s.site_name || 'BankoBet';
      const md = document.getElementById('meta-desc');
      if (md && !md.getAttribute('content')) md.setAttribute('content', s.site_description || '');
      const mk = document.getElementById('meta-keys');
      if (mk && !mk.getAttribute('content')) mk.setAttribute('content', s.site_keywords || '');
      const ht = document.getElementById('hero-title');
      if (ht && s.hero_title) {
        const words = s.hero_title.split(',');
        ht.innerHTML = words[0] + (words[1] ? ', <span class="hero-accent">' + words[1].trim() + '</span>' : '');
      }
      const hd = document.getElementById('hero-desc');
      if (hd && s.hero_description) hd.textContent = s.hero_description;
      const ft = document.getElementById('footer');
      if (ft && s.footer_text) ft.innerHTML = '<p>' + s.footer_text + '</p>';
    })
    .catch(() => {});
}

function loadPosts() {
  fetch('api/posts.php')
    .then(r => r.json())
    .then(posts => {
      const grid = document.getElementById('posts-grid');
      const empty = document.getElementById('empty-state');
      if (!posts.length) { empty.style.display = 'block'; return; }
      grid.innerHTML = posts.map(p => `
        <a href="post.html?s=${p.slug}" class="card">
          ${p.featured_image ? `<img class="card-img" src="${p.featured_image}" alt="${p.title}">` : '<div class="card-img"></div>'}
          <div class="card-body">
            ${p.category ? `<span class="card-cat">${p.category}</span>` : ''}
            <h3 class="card-title">${p.title}</h3>
            <p class="card-excerpt">${p.excerpt || p.content.replace(/<[^>]*>/g,'').substring(0,120) + '...'}</p>
            <span class="card-date">${new Date(p.created_at).toLocaleDateString('tr-TR')}</span>
          </div>
        </a>
      `).join('');
    })
    .catch(() => {});
}
