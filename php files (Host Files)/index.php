<?php
require_once 'dh.php';

// Fetch clients
$clients = $pdo->query("SELECT * FROM clients ORDER BY last_seen DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch commands with results
$commands = $pdo->query("
    SELECT c.id, c.client_id, c.command_text, c.status, c.created_at, r.stdout, r.stderr
    FROM commands c
    LEFT JOIN command_results r ON c.id = r.command_id
    ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Remote Control Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background-color: #0d1117;
    color: #c9d1d9;
    font-family: "Cairo", sans-serif;
    direction: rtl;
}
.sidebar {
    height: 100vh;
    background: #161b22;
    padding: 20px;
    position: fixed;
    width: 240px;
    top: 0;
    right: 0;
}
.sidebar h4 { color: #58a6ff; margin-bottom: 30px; text-align: center; }
.sidebar a { display: block; color: #c9d1d9; padding: 10px 15px; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
.sidebar a:hover { background-color: #21262d; color: #58a6ff; }
.main { margin-right: 260px; padding: 20px; }
.card { background: #161b22; border: 1px solid #30363d; border-radius: 12px; color: #c9d1d9; margin-bottom: 20px; }
.form-control, .form-select { background-color: #0d1117; border: 1px solid #30363d; color: #c9d1d9; }
.btn-primary { background-color: #238636; border: none; }
.btn-primary:hover { background-color: #2ea043; }
pre { background-color: #0d1117; border: 1px solid #30363d; padding: 10px; border-radius: 6px; color: #9ecbff; overflow-x:auto; }
@media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; position: relative; }
    .main { margin-right: 0; }
}
</style>
</head>
<body>

<div class="sidebar">
  <h4><i class="bi bi-terminal"></i> التحكم عن بعد</h4>
  <a href="#clients"><i class="bi bi-pc-display"></i> الأجهزة</a>
  <a href="#commands"><i class="bi bi-code-slash"></i> الأوامر</a>
  <a href="#send"><i class="bi bi-send"></i> إرسال أمر</a>
</div>

<div class="main">
  <h2 class="mb-4 text-info">لوحة تحكم النظام</h2>

  <!-- إرسال أمر -->
  <div id="send" class="card p-4">
    <h5 class="mb-3"><i class="bi bi-send"></i> إرسال أمر جديد</h5>
    <form id="commandForm">
      <div class="row mb-3">
        <div class="col-md-6 mb-2">
          <label>اختيار الجهاز</label>
          <select class="form-select" name="client_id" required>
            <option value="">اختر الجهاز...</option>
            <?php foreach ($clients as $c): ?>
              <option value="<?= htmlspecialchars($c['client_id']) ?>"><?= htmlspecialchars($c['client_id']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 mb-2">
          <label>أمر مخصص</label>
          <input type="text" class="form-control" name="command" placeholder="مثال: dir /s">
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="bi bi-play"></i> إرسال</button>
      <div id="status" class="mt-3"></div>
    </form>
  </div>

  <!-- الأجهزة -->
  <div id="clients" class="card p-3">
    <h5><i class="bi bi-pc-display"></i> الأجهزة المتصلة</h5>
    <table class="table table-dark table-striped" id="clientsTable">
      <thead>
        <tr>
          <th>Client ID</th>
          <th>آخر ظهور</th>
          <th>تاريخ التسجيل</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clients as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['client_id']) ?></td>
            <td><?= $c['last_seen'] ?: '-' ?></td>
            <td><?= $c['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- الأوامر -->
  <div id="commands" class="card p-3">
    <h5><i class="bi bi-terminal"></i> سجل الأوامر</h5>
    <table class="table table-dark table-hover" id="commandsTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Command</th>
          <th>Status</th>
          <th>Created</th>
          <th>Output</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($commands as $cmd): ?>
          <tr>
            <td><?= $cmd['id'] ?></td>
            <td><?= htmlspecialchars($cmd['client_id']) ?></td>
            <td><code><?= htmlspecialchars($cmd['command_text']) ?></code></td>
            <td><?= $cmd['status'] ?></td>
            <td><?= $cmd['created_at'] ?></td>
            <td>
              <?php if ($cmd['stdout'] || $cmd['stderr']): ?>
                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#res<?= $cmd['id'] ?>">عرض</button>
                <div class="modal fade" id="res<?= $cmd['id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-light">
                      <div class="modal-header border-secondary">
                        <h5>النتيجة - أمر رقم <?= $cmd['id'] ?></h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <h6 class="text-success">StdOut:</h6>
                        <pre><?= htmlspecialchars($cmd['stdout']) ?></pre>
                        <h6 class="text-danger">StdErr:</h6>
                        <pre><?= htmlspecialchars($cmd['stderr']) ?></pre>
                      </div>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
  $('#clientsTable, #commandsTable').DataTable({ pageLength: 6 });

  $('#commandForm').on('submit', function(e){
    e.preventDefault();
    $.post('send_command.php', $(this).serialize(), function(res){
      if(res.status === 'success'){
        $('#status').html('<div class="text-success">✅ تم إرسال الأمر بنجاح</div>');
        setTimeout(()=> location.reload(), 1000);
      } else {
        $('#status').html('<div class="text-danger">❌ '+res.message+'</div>');
      }
    }, 'json').fail(function(){
      $('#status').html('<div class="text-danger">⚠️ حدث خطأ أثناء الاتصال بالخادم</div>');
    });
  });
});
</script>

</body>
</html>
