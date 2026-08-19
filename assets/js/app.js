// =====================================================================
// app.js — AJAX handler (vanilla JS, tanpa library)
// =====================================================================

function showAlert(msg, ok = true) {
  const box = document.getElementById('alert-box');
  if (!box) return;
  box.innerHTML = `<div class="alert ${ok ? 'alert-ok' : 'alert-err'}">${msg}</div>`;
  box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  setTimeout(() => { box.innerHTML = ''; }, 3500);
}

// ---------- KRITERIA: simpan via AJAX ----------
function simpanKriteria(btn) {
  const tr = btn.closest('tr');
  const id = tr.dataset.id;
  const bobot = tr.querySelector('.in-bobot').value;
  const atribut = tr.querySelector('.in-atribut').value;

  const body = new URLSearchParams({ id, bobot, atribut });
  fetch('api/kriteria_save.php', { method: 'POST', body })
    .then(r => r.json())
    .then(d => {
      showAlert(d.msg, d.ok);
      if (d.ok && d.total_bobot !== undefined) {
        document.getElementById('total-bobot').textContent = d.total_bobot;
      }
    })
    .catch(() => showAlert('Terjadi kesalahan koneksi.', false));
}

// ---------- ALTERNATIF ----------
let ALT_DATA = [];

function loadAlternatif() {
  fetch('api/alternatif.php?action=list')
    .then(r => r.json())
    .then(d => { ALT_DATA = d.data || []; renderAlternatif(ALT_DATA); });
}

function renderAlternatif(rows) {
  const body = document.getElementById('alt-body');
  if (!body) return;
  if (rows.length === 0) {
    body.innerHTML = `<tr><td colspan="13" class="empty">Belum ada data.</td></tr>`;
    return;
  }
  body.innerHTML = rows.map(r => `
    <tr>
      <td><b>${esc(r.kode)}</b></td>
      <td>${esc(r.nama_menu)}</td>
      <td>${esc(r.nama_pembeli)}</td>
      <td>${fmtWaktu(r.waktu_pembelian)}</td>
      <td class="c">${r.c1}</td><td class="c">${r.c2}</td><td class="c">${r.c3}</td><td class="c">${r.c4}</td>
      <td class="c">${r.c5}</td><td class="c">${r.c6}</td><td class="c">${r.c7}</td><td class="c">${r.c8}</td>
      <td class="c no-print">
        <button class="btn btn-ghost btn-sm" onclick="editAlt(${r.id})">Edit</button>
        <button class="btn btn-danger btn-sm" onclick="hapusAlt(${r.id})">Hapus</button>
      </td>
    </tr>`).join('');
}

function esc(s) { return String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c])); }
function fmtWaktu(w) {
  const d = new Date(w.replace(' ', 'T'));
  if (isNaN(d)) return w;
  return d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function editAlt(id) {
  fetch('api/alternatif.php?action=get&id=' + id)
    .then(r => r.json())
    .then(d => {
      const a = d.data;
      document.getElementById('alt-id').value = a.id;
      document.getElementById('alt-kode').value = a.kode;
      document.getElementById('alt-menu').value = a.nama_menu;
      document.getElementById('alt-pembeli').value = a.nama_pembeli;
      document.getElementById('alt-waktu').value = a.waktu_pembelian.replace(' ', 'T').slice(0, 16);
      for (let i = 1; i <= 8; i++) {
        const sel = document.querySelector(`[name="c${i}"]`);
        if (sel) sel.value = a['c' + i];
      }
      document.getElementById('form-title').textContent = 'Edit Data Pembelian';
      document.getElementById('btn-submit').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Perbarui Data';
      document.getElementById('btn-reset').style.display = 'inline-flex';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function resetForm() {
  const f = document.getElementById('form-alt');
  f.reset();
  document.getElementById('alt-id').value = '';
  document.getElementById('form-title').textContent = 'Tambah Data Pembelian';
  document.getElementById('btn-submit').innerHTML = '<i class="fa-solid fa-plus"></i> Simpan Data';
  document.getElementById('btn-reset').style.display = 'none';
}

function hapusAlt(id) {
  if (!confirm('Hapus data alternatif ini?')) return;
  fetch('api/alternatif.php', { method: 'POST', body: new URLSearchParams({ action: 'delete', id }) })
    .then(r => r.json())
    .then(d => { showAlert(d.msg, d.ok); if (d.ok) loadAlternatif(); });
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-alt');
  if (form) {
    loadAlternatif();
    form.addEventListener('submit', e => {
      e.preventDefault();
      const fd = new FormData(form);
      fd.append('action', 'save');
      fetch('api/alternatif.php', { method: 'POST', body: new URLSearchParams(fd) })
        .then(r => r.json())
        .then(d => {
          showAlert(d.msg, d.ok);
          if (d.ok) { resetForm(); loadAlternatif(); }
        })
        .catch(() => showAlert('Terjadi kesalahan koneksi.', false));
    });
  }

  // pencarian alternatif (client-side)
  const search = document.getElementById('search-alt');
  if (search) {
    search.addEventListener('input', () => {
      const q = search.value.toLowerCase();
      renderAlternatif(ALT_DATA.filter(r =>
        r.nama_menu.toLowerCase().includes(q) || r.nama_pembeli.toLowerCase().includes(q) || r.kode.toLowerCase().includes(q)
      ));
    });
  }
});
