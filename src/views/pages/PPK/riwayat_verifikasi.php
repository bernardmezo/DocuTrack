<?php
// File: src/views/pages/PPK/riwayat.php
$list_riwayat = $list_riwayat ?? [];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section class="bg-white p-6 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Riwayat Persetujuan PPK</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar kegiatan yang telah Anda setujui atau tolak.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status Akhir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($list_riwayat)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada riwayat persetujuan.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($list_riwayat as $item): 
                            $status = $item['status'];
                            $badgeClass = ($status === 'Disetujui') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                            $icon = ($status === 'Disetujui') ? 'fa-check-circle' : 'fa-times-circle';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo $no++; ?>.</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($item['nama']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo htmlspecialchars($item['pengusul']); ?>
                                <div class="text-xs text-gray-400"><?php echo htmlspecialchars($item['prodi']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo date('d M Y', strtotime($item['tanggal_pengajuan'])); ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                    <i class="fas <?php echo $icon; ?>"></i> <?php echo $status; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <!-- Link ke Detail (Readonly) -->
                                <a href="/docutrack/public/ppk/telaah/show/<?php echo $item['id']; ?>?ref=riwayat-verifikasi"
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </section>
</main>