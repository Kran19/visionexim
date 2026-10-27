<?php 
/**
 * Vision Exim — Inquiry View & Actions
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Inquiry Details';
$current_module = 'inquiries';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php?error=' . urlencode('Invalid inquiry ID.'));
    exit;
}

// Fetch current inquiry record
$stmt = $conn->prepare("SELECT * FROM inquiries WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$inq = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$inq) {
    header('Location: list.php?error=' . urlencode('Inquiry not found.'));
    exit;
}

$errors = [];
$success_msg = '';

// Handle POST actions: Status change, internal notes, simulated reply
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_notes') {
        $notes = trim($_POST['internal_notes'] ?? '');
        $stmt = $conn->prepare("UPDATE inquiries SET internal_notes = ? WHERE id = ?");
        $stmt->bind_param('si', $notes, $id);
        if ($stmt->execute()) {
            $success_msg = 'Internal notes updated successfully!';
            $inq['internal_notes'] = $notes;
        } else {
            $errors[] = 'Database error updating notes: ' . $conn->error;
        }
        $stmt->close();
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'save_status') {
        $status = trim($_POST['status'] ?? 'new');
        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        if ($stmt->execute()) {
            $success_msg = 'Inquiry status updated to ' . ucfirst($status) . '!';
            $inq['status'] = $status;
        } else {
            $errors[] = 'Database error updating status: ' . $conn->error;
        }
        $stmt->close();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'send_reply') {
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        
        // Simulating email dispatch + automatically updating status to 'replied' in database!
        $status = 'replied';
        $stmt = $conn->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        if ($stmt->execute()) {
            header('Location: list.php?success=' . urlencode('Quotation sent to ' . $inq['company_name'] . ' and status updated to Replied!'));
            exit;
        } else {
            $errors[] = 'Database error updating reply state: ' . $conn->error;
        }
        $stmt->close();
    }
}

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                    <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="list.php" class="hover:text-spice-green-600 transition-colors">Inquiries</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Inquiry Details</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Inquiry #INQ-<?= str_pad($inq['id'], 3, '0', STR_PAD_LEFT) ?></h1>
                    
                    <?php 
                    $status_class = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300';
                    if ($inq['status'] === 'new') {
                        $status_class = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400';
                    } elseif ($inq['status'] === 'progress') {
                        $status_class = 'bg-spice-turmeric-100 text-spice-turmeric-600 dark:bg-spice-turmeric-950 dark:text-spice-turmeric-400';
                    } elseif ($inq['status'] === 'replied') {
                        $status_class = 'bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400';
                    }
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?= $status_class ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $inq['status'] === 'new' ? 'bg-emerald-500 animate-pulse' : ($inq['status'] === 'progress' ? 'bg-spice-turmeric-500' : ($inq['status'] === 'replied' ? 'bg-blue-500' : 'bg-gray-400')) ?>"></span>
                        <?= ucfirst(htmlspecialchars($inq['status'] === 'progress' ? 'In Progress' : $inq['status'])) ?>
                    </span>
                </div>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Received on <?= date('M d, Y \a\t h:i A', strtotime($inq['created_at'])) ?> via <?= ucfirst(htmlspecialchars($inq['source'])) ?></p>
            </div>
            <div>
                <a href="list.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-100 dark:border-slate-700 text-[12px] font-semibold hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:border-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-[10px]"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Success/Error Banner -->
        <?php if (!empty($success_msg)): ?>
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13px] font-medium flex items-center gap-2 animate-bounce">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <?= $success_msg ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13px] font-medium">
            <?php foreach ($errors as $err): ?>
            <p class="mb-1"><i class="fas fa-exclamation-circle text-red-500 mr-1.5"></i> <?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Columns - Buyer Details & Message -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Inquiry Details Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-spice-green-600/10 dark:bg-spice-green-600/20 flex items-center justify-center text-spice-green-600">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Buyer Details</h2>
                        </div>
                        <span class="text-[13px] text-gray-600 dark:text-slate-400 font-semibold flex items-center gap-1.5">
                            <span><?= htmlspecialchars($inq['country_flag']) ?></span> <?= htmlspecialchars($inq['country_name']) ?> Buyer
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Contact Name</p>
                            <p class="text-[13px] font-semibold text-spice-dark dark:text-white mt-1"><?= htmlspecialchars($inq['contact_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Company Name</p>
                            <p class="text-[13px] font-semibold text-spice-dark dark:text-white mt-1"><?= htmlspecialchars($inq['company_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Email Address</p>
                            <a href="mailto:<?= htmlspecialchars($inq['email']) ?>" class="text-[13px] font-semibold text-spice-green-600 hover:underline mt-1 block"><?= htmlspecialchars($inq['email']) ?></a>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Phone / WhatsApp</p>
                            <p class="text-[13px] font-semibold text-spice-dark dark:text-white mt-1"><?= htmlspecialchars($inq['phone'] ?? '—') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Product Specifications Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-turmeric-500/10 dark:bg-spice-turmeric-500/20 flex items-center justify-center text-spice-turmeric-500">
                            <i class="fas fa-boxes-stacked text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Requirements</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Requested Product</p>
                            <div class="flex items-center gap-2.5 mt-1.5">
                                <span class="w-6 h-6 rounded bg-spice-turmeric-50 dark:bg-spice-turmeric-900/30 flex items-center justify-center text-spice-turmeric-500 text-xs">
                                    <i class="fas fa-pepper-hot"></i>
                                </span>
                                <span class="text-[13px] font-semibold text-spice-dark dark:text-white"><?= htmlspecialchars($inq['requested_product']) ?></span>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-semibold mb-2">Original Message</p>
                            <div class="bg-spice-cream/30 dark:bg-slate-700/30 p-4 rounded-xl border border-gray-100/50 dark:border-slate-700 text-[13px] text-gray-600 dark:text-slate-300 leading-relaxed italic">
                                "<?= nl2br(htmlspecialchars($inq['message'])) ?>"
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Responder Form -->
                <!-- <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center text-blue-500">
                            <i class="fas fa-reply text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Reply via Email</h2>
                    </div>

                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="send_reply">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">From</label>
                                <input type="text" value="exports@visionexim.com" readonly
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">To</label>
                                <input type="text" value="<?= htmlspecialchars($inq['email']) ?>" readonly
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject</label>
                            <input type="text" name="subject" value="Re: Export Inquiry for <?= htmlspecialchars($inq['requested_product']) ?> — Vision Exim India" required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Message Body</label>
                            <textarea rows="6" name="body" required
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">Dear <?= htmlspecialchars($inq['contact_name']) ?>,

Thank you for your interest in our premium <?= htmlspecialchars($inq['requested_product']) ?>. 

For a trial quantity of <?= htmlspecialchars($inq['quantity']) ?>, we would be pleased to offer you our premium export standard. 

Please find our product catalog and specification sheet attached. Let us know your preferred shipment terms (FOB/CIF) to finalize the quotation.

Best Regards,
Vision Exim Export Team</textarea>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <button type="button" onclick="showToast('Catalog sheet attached!', 'info')" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700 text-[12px] text-gray-600 dark:text-slate-300 font-semibold">
                                <i class="fas fa-paperclip mr-2"></i>Attach Catalog
                            </button>
                            <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 hover:from-spice-green-700 hover:to-spice-green-800 text-white text-[12px] font-semibold shadow-lg shadow-spice-green-600/25 transform hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-paper-plane mr-2"></i>Send Quotation
                            </button>
                        </div>
                    </form>
                </div> -->

            </div>

            <!-- Right Column - Admin Controls -->
            <div class="space-y-6">
                
                <!-- Inquiry Status Control -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 flex items-center justify-center text-purple-500">
                            <i class="fas fa-sliders text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Action Center</h2>
                    </div>

                    <div class="space-y-5">
                        <form method="POST" action="" class="space-y-4">
                            <input type="hidden" name="action" value="save_status">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Inquiry Status</label>
                                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer">
                                    <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                    <option value="progress" <?= $inq['status'] === 'progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="replied" <?= $inq['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                                    <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </div>
                        </form>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Inquiry Source</label>
                            <input type="text" value="<?= ucfirst(htmlspecialchars($inq['source'])) ?> Form" readonly
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none">
                        </div>

                        <div class="pt-2 border-t border-gray-100 dark:border-slate-700 flex flex-col gap-3">
                            <?php if (!empty($inq['phone'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $inq['phone']) ?>" target="_blank"
                               class="w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-[12px] font-semibold flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 transform hover:-translate-y-0.5 transition-all">
                                <i class="fab fa-whatsapp text-sm"></i> Chat on WhatsApp
                            </a>
                            <?php endif; ?>
                            <a href="mailto:<?= htmlspecialchars($inq['email']) ?>"
                               class="w-full py-3 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 text-[12px] font-semibold flex items-center justify-center gap-2 transition-all">
                                <i class="fas fa-envelope text-xs"></i> Direct Email Client
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Internal Notes -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-turmeric-500/10 dark:bg-spice-turmeric-500/20 flex items-center justify-center text-spice-turmeric-500">
                            <i class="fas fa-clipboard text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Internal Notes</h2>
                    </div>

                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="save_notes">
                        <div>
                            <textarea rows="5" name="internal_notes" placeholder="Write internal notes about negotiations, special specifications requested, background checks, etc. (Not visible to buyer)"
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"><?= htmlspecialchars($inq['internal_notes'] ?? '') ?></textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-3 rounded-xl bg-spice-dark dark:bg-slate-700 text-white dark:text-slate-200 text-[12px] font-semibold hover:bg-spice-dark/90 dark:hover:bg-slate-600 transition-all shadow-md">
                            Save Notes
                        </button>
                    </form>
                </div>

                <!-- Delete Actions Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <button onclick="deleteSingleInquiry(<?= $inq['id'] ?>, '<?= htmlspecialchars($inq['company_name'], ENT_QUOTES) ?>')"
                       class="block w-full py-3.5 rounded-xl bg-spice-chili-50 dark:bg-spice-chili-900/20 text-spice-chili-500 text-[13px] font-semibold text-center hover:bg-spice-chili-100 dark:hover:bg-spice-chili-900/40 transition-colors">
                        Delete Inquiry
                    </button>
                </div>

            </div>

        </div>

    </div>
</main>

<script>
function deleteSingleInquiry(id, name) {
    confirmDelete(name).then((result) => {
        if(result.isConfirmed) {
            fetch('delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Inquiry deleted successfully', 'success');
                    setTimeout(() => window.location.href = 'list.php', 1000);
                } else {
                    showToast(data.message || 'Failed to delete inquiry', 'error');
                }
            })
            .catch(() => showToast('Server error.', 'error'));
        }
    });
}
</script>

<?php include '../../includes/footer.php'; ?>
