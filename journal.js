function saveEntry() {
  const entry = document.getElementById('entryBody').value;

  localStorage.setItem('journalEntry', entry);

  alert('Entry Saved!');
}

window.onload = () => {
  const saved = localStorage.getItem('journalEntry');

  if (saved) {
    document.getElementById('entryBody').value = saved;
  }
};

  var STORAGE_KEY = 'mindspace_journal';

  function loadEntries() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
    catch(e) { return []; }
  }

  function saveEntries(entries) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(entries)); } catch(e) {}
  }

  var entries = loadEntries();
  var currentId = null;

  function renderList() {
    var list = document.getElementById('entriesList');
    list.innerHTML = '';
    if (entries.length === 0) {
      list.innerHTML = '<div style="padding:14px 18px; font-size:0.82rem; color:var(--text-light);">No entries yet.</div>';
    }
    entries.forEach(function(e) {
      var div = document.createElement('div');
      div.className = 'entry-item' + (e.id === currentId ? ' active' : '');
      div.innerHTML =
        '<div class="ei-date">' + e.date + '</div>' +
        '<div class="ei-title">' + (e.title || 'Untitled') + '</div>' +
        '<div class="ei-preview">' + (e.body || '') + '</div>';
      div.onclick = function() { loadEntry(e.id); };
      list.appendChild(div);
    });
    document.getElementById('entryCountLabel').textContent =
      entries.length + ' entr' + (entries.length === 1 ? 'y' : 'ies');
  }

  function loadEntry(id) {
    currentId = id;
    var e = entries.find(function(x) { return x.id === id; });
    if (!e) return;
    document.getElementById('entryTitle').value = e.title || '';
    document.getElementById('entryBody').value  = e.body  || '';
    document.getElementById('editorDate').textContent = e.date || '';
    updateWordCount();
    renderList();
  }

  function newEntry() {
    var today = new Date().toLocaleDateString('en-UG', { year:'numeric', month:'long', day:'numeric' });
    var newE = { id: Date.now(), date: today, title: '', body: '' };
    entries.unshift(newE);
    saveEntries(entries);
    renderList();
    loadEntry(newE.id);
    document.getElementById('entryTitle').focus();
  }

  function saveEntry() {
    if (currentId === null) { newEntry(); return; }
    var idx = entries.findIndex(function(e) { return e.id === currentId; });
    if (idx === -1) return;
    entries[idx].title = document.getElementById('entryTitle').value;
    entries[idx].body  = document.getElementById('entryBody').value;
    saveEntries(entries);
    renderList();
    var sm = document.getElementById('savedMsg');
    sm.style.display = 'inline';
    setTimeout(function() { sm.style.display = 'none'; }, 2000);
  }

  var autoSave;
  function triggerAutoSave() {
    clearTimeout(autoSave);
    autoSave = setTimeout(saveEntry, 2000);
  }
  document.getElementById('entryBody').addEventListener('input', triggerAutoSave);
  document.getElementById('entryTitle').addEventListener('input', triggerAutoSave);

  function updateWordCount() {
    var text = document.getElementById('entryBody').value;
    var words = text.trim() ? text.trim().split(/\s+/).length : 0;
    document.getElementById('wordCount').textContent = words + ' word' + (words === 1 ? '' : 's');
  }

  document.getElementById('editorDate').textContent =
    new Date().toLocaleDateString('en-UG', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

  renderList();
  if (entries.length) { loadEntry(entries[0].id); }