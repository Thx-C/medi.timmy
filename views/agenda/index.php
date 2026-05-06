<?php
$pageTitle   = 'Agenda';
$activeRoute = 'agenda';
$canEdit     = RoleMiddleware::can('gerer_agenda');
ob_start();
?>

<div class="page-header flex justify-between items-center">
  <div>
    <h1>Agenda</h1>
    <p>Calendrier des rendez-vous<?= $canEdit ? ' — glissez-déposez pour déplacer' : ' — lecture seule' ?></p>
  </div>
  <?php if ($canEdit): ?>
  <button class="btn btn-primary" id="btn-new-rdv">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Nouveau RDV
  </button>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

<div class="modal-overlay" id="modal-detail">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Détail du rendez-vous</span>
      <button class="modal-close" onclick="closeModal('modal-detail')">✕</button>
    </div>
    <div class="modal-body" id="modal-detail-body"></div>
  </div>
</div>

<div class="modal-overlay" id="modal-rdv">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="modal-rdv-title">Nouveau rendez-vous</span>
      <button class="modal-close" onclick="closeModal('modal-rdv')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="rdv-id" value="">
      
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="rdv-patient">Patient</label>
          <select class="form-control" id="rdv-patient" required>
            <option value="">— Sélectionner —</option>
            <?php foreach ($patients ?? [] as $p): ?>
            <option value="<?= $p['id'] ?>"><?= e($p['nom'] . ' ' . $p['prenom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="rdv-praticien">Praticien</label>
          <select class="form-control" id="rdv-praticien" required>
            <option value="">— Sélectionner —</option>
            <?php foreach ($praticiens ?? [] as $pr): ?>
            <option value="<?= $pr['id'] ?>"><?= e('Dr. ' . $pr['nom'] . ' ' . $pr['prenom']) ?> (<?= e($pr['role_label']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="rdv-datetime">Date & Heure</label>
          <input type="datetime-local" class="form-control" id="rdv-datetime" required>
        </div>
        
        <div class="form-group">
          <label class="form-label" for="rdv-duree">Durée</label>
          <select class="form-control" id="rdv-duree">
            <option value="15">15 min</option>
            <option value="30" selected>30 min</option>
            <option value="45">45 min</option>
            <option value="60">1 heure</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label class="form-label" for="rdv-motif">Motif</label>
        <input type="text" class="form-control" id="rdv-motif" placeholder="Ex: Consultation générale">
      </div>
      
      <div class="form-group">
        <label class="form-label" for="rdv-statut">Statut</label>
        <select class="form-control" id="rdv-statut">
          <option value="planifie">Planifié</option>
          <option value="confirme">Confirmé</option>
          <option value="annule">Annulé</option>
          <option value="termine">Terminé</option>
        </select>
      </div>
      
      <div class="flex gap-2" style="justify-content:flex-end; margin-top:8px;">
        <button class="btn btn-ghost" onclick="closeModal('modal-rdv')">Annuler</button>
        <button class="btn btn-danger btn-sm" id="btn-cancel-rdv" style="display:none" onclick="cancelRdv()">Annuler le RDV</button>
        <button class="btn btn-primary" onclick="saveRdv()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<?php
$extraHead = '
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
';
$extraScript = <<<JS
<script>
const canEdit = {$canEdit};

function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openModal(id)  { document.getElementById(id).classList.add('open'); }

document.addEventListener('DOMContentLoaded', function() {
  const calEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calEl, {
    locale: 'fr',
    initialView: 'timeGridWeek',
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    slotMinTime: '07:00:00',
    slotMaxTime: '20:00:00',
    allDaySlot: false,
    height: 'auto',
    editable: canEdit,
    eventDurationEditable: false,
    nowIndicator: true,
    businessHours: { daysOfWeek:[1,2,3,4,5], startTime:'08:00', endTime:'19:00' },

    events: function(info, success, failure) {
      fetch(BASE_URL + '/index.php?route=agenda.events')
        .then(r => r.json()).then(success).catch(failure);
    },

    // Drag & drop
    eventDrop: function(info) {
      if (!canEdit) { info.revert(); return; }
      const start = info.event.start.toISOString().slice(0,19).replace('T',' ');
      apiPost('agenda.move', { id: info.event.id, start })
        .then(res => {
          if (res.success) toast('RDV déplacé', 'success');
          else { toast('Erreur lors du déplacement', 'error'); info.revert(); }
        });
    },

    // Click sur un event
    eventClick: function(info) {
      const p = info.event.extendedProps;
      if (canEdit) {
        document.getElementById('rdv-id').value = info.event.id;
        document.getElementById('modal-rdv-title').textContent = 'Modifier le rendez-vous';
        document.getElementById('btn-cancel-rdv').style.display = 'inline-flex';
        
        document.getElementById('rdv-patient').value = p.patient_id || '';
        document.getElementById('rdv-praticien').value = p.praticien_id || '';
        document.getElementById('rdv-motif').value = p.motif || '';
        document.getElementById('rdv-statut').value = p.statut || 'planifie';

        // Gérer la date et la durée
        const dt = info.event.start;
        const local = new Date(dt - dt.getTimezoneOffset()*60000).toISOString().slice(0,16);
        document.getElementById('rdv-datetime').value = local;

        // Si une durée a été envoyée dans les paramètres de l'événement, on l'affiche. Sinon, on garde la valeur par défaut ou on prend celle du plugin.
        if (p.duree_minutes) {
          document.getElementById('rdv-duree').value = p.duree_minutes;
        }

        openModal('modal-rdv');
      } else {
        document.getElementById('modal-detail-body').innerHTML = `
          <p><strong>Patient :</strong> \${p.patient || '—'}</p>
          <p style="margin-top:8px"><strong>Praticien :</strong> \${p.praticien || '—'}</p>
          <p style="margin-top:8px"><strong>Motif :</strong> \${p.motif || '—'}</p>
          <p style="margin-top:8px"><strong>Statut :</strong> \${p.statut || '—'}</p>
          <p style="margin-top:8px"><strong>Date :</strong> \${info.event.start.toLocaleString('fr-FR')}</p>
          <p style="margin-top:16px;color:var(--gray-500);font-size:.8rem;">Pour modifier ce rendez-vous, contactez la secrétaire par téléphone.</p>
        `;
        openModal('modal-detail');
      }
    },

    // Click sur un créneau vide
    dateClick: function(info) {
      if (!canEdit) return;
      document.getElementById('rdv-id').value = '';
      document.getElementById('modal-rdv-title').textContent = 'Nouveau rendez-vous';
      document.getElementById('btn-cancel-rdv').style.display = 'none';
      document.getElementById('rdv-patient').value = '';
      document.getElementById('rdv-praticien').value = '';
      document.getElementById('rdv-motif').value = '';
      document.getElementById('rdv-statut').value = 'planifie';
      
      const dt = info.date;
      const local = new Date(dt - dt.getTimezoneOffset()*60000).toISOString().slice(0,16);
      document.getElementById('rdv-datetime').value = local;
      
      // Gère la durée par défaut (30 min sélectionnées) ou laisse telle quelle
      document.getElementById('rdv-duree').value = '30';
      
      openModal('modal-rdv');
    }
  });

  calendar.render();

  document.getElementById('btn-new-rdv')?.addEventListener('click', () => {
    document.getElementById('rdv-id').value = '';
    document.getElementById('modal-rdv-title').textContent = 'Nouveau rendez-vous';
    document.getElementById('btn-cancel-rdv').style.display = 'none';
    document.getElementById('rdv-patient').value = '';
    document.getElementById('rdv-praticien').value = '';
    
    // Initialisation de la date du jour avec la date et l'heure actuelles (au format YYYY-MM-DDTHH:MM)
    const now = new Date();
    const localNow = new Date(now - now.getTimezoneOffset()*60000).toISOString().slice(0,16);
    document.getElementById('rdv-datetime').value = localNow;
    
    document.getElementById('rdv-duree').value = '30';
    document.getElementById('rdv-motif').value = '';
    document.getElementById('rdv-statut').value = 'planifie';
    openModal('modal-rdv');
  });

  window.calendar = calendar;
});

function saveRdv() {
  const id = document.getElementById('rdv-id').value;
  const data = {
    patient_id:    document.getElementById('rdv-patient').value,
    praticien_id:  document.getElementById('rdv-praticien').value,
    date_heure:    document.getElementById('rdv-datetime').value.replace('T',' ') + ':00',
    duree_minutes: document.getElementById('rdv-duree').value,
    motif:         document.getElementById('rdv-motif').value,
    statut:        document.getElementById('rdv-statut').value,
  };
  const route = id ? 'agenda.update' : 'agenda.create';
  if (id) data.id = id;

  apiPost(route, data).then(res => {
    if (res.success) {
      toast(id ? 'RDV mis à jour' : 'RDV créé', 'success');
      closeModal('modal-rdv');
      window.calendar.refetchEvents();
    } else {
      toast('Erreur lors de la sauvegarde', 'error');
    }
  });
}

function cancelRdv() {
  if (!confirm('Annuler ce rendez-vous ?')) return;
  const id = document.getElementById('rdv-id').value;
  apiPost('agenda.cancel', { id }).then(res => {
    if (res.success) {
      toast('RDV annulé', 'info');
      closeModal('modal-rdv');
      window.calendar.refetchEvents();
    }
  });
}
</script>
JS;

$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
