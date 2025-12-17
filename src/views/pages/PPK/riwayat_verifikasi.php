<?php
// File: src/views/pages/PPK/riwayat.php
$list_riwayat = $list_riwayat ?? [];
?>

<main class="main-content font-poppins p-3 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i>
                <span>Riwayat Persetujuan PPK</span>
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar kegiatan yang telah Anda setujui atau tolak.</p>
        </div>

        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 600px;">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pengusul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status Akhir</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <?php if (empty($list_riwayat)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <div class="empty-state-text">Belum ada riwayat persetujuan.</div>
                                        <div class="empty-state-subtext">Riwayat akan muncul setelah Anda menyetujui atau menolak usulan</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($list_riwayat as $item): 
                                $status = $item['status'];
                                $badgeClass = ($status === 'Disetujui') ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200';
                                $icon = ($status === 'Disetujui') ? 'fa-check-circle' : 'fa-times-circle';
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700"><?php echo $no++; ?>.</td>
                                <td class="px-6 py-5 text-sm">
                                    <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['nama']); ?></span>
                                </td>
                                <td class="px-6 py-5 text-sm">
                                    <div class="flex flex-col">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($item['pengusul']); ?></span>
                                        <span class="text-xs text-gray-500 mt-0.5 font-medium">
                                            <i class="fas fa-graduation-cap mr-1"></i><?php echo htmlspecialchars($item['prodi']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                                        <?php echo date('d M Y', strtotime($item['tanggal_pengajuan'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border <?php echo $badgeClass; ?>">
                                        <i class="fas <?php echo $icon; ?>"></i> <?php echo $status; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                                    <a href="/docutrack/public/ppk/telaah/show/<?php echo $item['id']; ?>?ref=riwayat-verifikasi"
                                       class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-blue-700 hover:shadow-md transition-all">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 600px;">
            <div class="p-3 space-y-3">
                <?php if (empty($list_riwayat)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <div class="empty-state-text">Belum ada riwayat persetujuan.</div>
                        <div class="empty-state-subtext">Riwayat akan muncul setelah Anda menyetujui atau menolak usulan</div>
                    </div>
                <?php else: ?>
                    <?php 
                    $no = 1;
                    foreach ($list_riwayat as $item): 
                        $status = $item['status'];
                        $statusClass = ($status === 'Disetujui') ? 'status-disetujui' : 'status-ditolak';
                        $icon = ($status === 'Disetujui') ? 'fa-check-circle' : 'fa-times-circle';
                    ?>
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <span class="mobile-card-number">#<?php echo $no++; ?></span>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <i class="fas <?php echo $icon; ?>"></i>
                                <?php echo $status; ?>
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan"><?php echo htmlspecialchars($item['nama']); ?></div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-pengusul">
                                <?php echo htmlspecialchars($item['pengusul']); ?>
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                <?php echo htmlspecialchars($item['prodi']); ?>
                            </div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Pengajuan
                            </div>
                            <div class="mobile-card-value"><?php echo date('d M Y', strtotime($item['tanggal_pengajuan'])); ?></div>
                        </div>
                        
                        <div class="mobile-card-actions">
                            <a href="/docutrack/public/ppk/telaah/show/<?php echo $item['id']; ?>?ref=riwayat-verifikasi" class="mobile-card-btn mobile-card-btn-primary">
                                <i class="fas fa-eye"></i>
                                Detail
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </section>
</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-row:last-of-type {
        margin-bottom: 0;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-label i {
        color: #3b82f6;
        font-size: 0.75rem;
    }
    
    .mobile-card-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }
    
    .mobile-card-pengusul {
        font-size: 0.85rem;
        color: #4b5563;
        margin-top: 0.25rem;
    }
    
    .mobile-card-prodi {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 0.5rem;
    }
    
    .mobile-card-btn {
        flex: 1;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
    
    .mobile-card-btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
    }
    
    .mobile-card-btn-primary:active {
        transform: scale(0.98);
    }
    
    .mobile-card-btn i {
        font-size: 0.875rem;
    }
    
    /* Status Badge Styling */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    .status-disetujui {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }
    
    .status-ditolak {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .empty-state-subtext {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
    
    /* Smooth animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .mobile-card {
        animation: slideIn 0.3s ease forwards;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .mobile-card {
            animation-delay: 0.1s;
        }
        
        .mobile-card:nth-child(2) {
            animation-delay: 0.15s;
        }
        
        .mobile-card:nth-child(3) {
            animation-delay: 0.2s;
        }
        
        .mobile-card:nth-child(4) {
            animation-delay: 0.25s;
        }
        
        .mobile-card:nth-child(5) {
            animation-delay: 0.3s;
        }
    }
</style>