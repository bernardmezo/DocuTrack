-- ===============================================================================
-- DOCUTRACK PERFORMANCE INDEXES
-- ===============================================================================
-- Created based on analysis of query patterns in Models/
-- ===============================================================================

-- 1. Optimize Dashboard Filtering by Jurusan
-- Used in: AdminModel::getDashboardKAKByJurusan
-- Query: WHERE jurusanPenyelenggara = ? ORDER BY createdAt DESC
CREATE INDEX idx_kegiatan_jurusan_created ON tbl_kegiatan(jurusanPenyelenggara, createdAt DESC);

-- 2. Optimize Text Search for Proposals
-- Used in: WadirModel, SuperAdminModel, PpkModel search features
-- Query: LIKE %keyword% on namaKegiatan or pemilikKegiatan
CREATE INDEX idx_kegiatan_nama ON tbl_kegiatan(namaKegiatan);
CREATE INDEX idx_kegiatan_pemilik ON tbl_kegiatan(pemilikKegiatan);

-- 3. Optimize Revision/Rejection History Lookup
-- Used in: AdminModel::getKomentarTerbaru, getKomentarPenolakan
-- Query: WHERE activitiesId = ? AND statusId = ? ORDER BY id DESC LIMIT 1
CREATE INDEX idx_history_lookup ON tbl_progress_history(kegiatanId, statusId, progressHistoryId DESC);

-- 4. Optimize LPJ Dashboard Sorting
-- Used in: AdminModel::getDashboardLPJ
-- Query: ORDER BY approvedAt/submittedAt logic
CREATE INDEX idx_lpj_dashboard ON tbl_lpj(submittedAt, approvedAt);

-- 5. Optimize Notification Fetching
-- Used in: LogStatusModel::getUnread, countUnread
-- Query: WHERE user_id = ? AND tipe_log LIKE 'NOTIFIKASI_%' AND status = 'BELUM_DIBACA'
-- Note: 'status' is equality, should be before 'tipe_log' (range/like)
CREATE INDEX idx_log_notification ON tbl_log_status(user_id, status, tipe_log);

-- 6. Optimize User Search
-- Used in: SuperAdminModel user management
CREATE INDEX idx_user_nama ON tbl_user(nama);

-- ===============================================================================
-- FOREIGN KEY INTEGRITY CHECK (Note: Already enforced in schema.sql)
-- ===============================================================================
-- The analyzed schema (merged_schema.sql) correctly defines CONSTRAINT ... FOREIGN KEY
-- with ON UPDATE CASCADE / ON DELETE CASCADE where appropriate.
-- No additional constraints needed.
