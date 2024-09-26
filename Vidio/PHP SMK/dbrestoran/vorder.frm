TYPE=VIEW
query=select `dbrestoran`.`tblorder`.`idorder` AS `idorder`,`dbrestoran`.`tblorder`.`idpelanggan` AS `idpelanggan`,`dbrestoran`.`tblorder`.`tglorder` AS `tglorder`,`dbrestoran`.`tblorder`.`total` AS `total`,`dbrestoran`.`tblorder`.`bayar` AS `bayar`,`dbrestoran`.`tblorder`.`kembali` AS `kembali`,`dbrestoran`.`tblorder`.`status` AS `status`,`dbrestoran`.`tblpelanggan`.`pelanggan` AS `pelanggan`,`dbrestoran`.`tblpelanggan`.`alamat` AS `alamat`,`dbrestoran`.`tblpelanggan`.`telp` AS `telp`,`dbrestoran`.`tblpelanggan`.`email` AS `email`,`dbrestoran`.`tblpelanggan`.`password` AS `password`,`dbrestoran`.`tblpelanggan`.`aktif` AS `aktif` from (`dbrestoran`.`tblpelanggan` join `dbrestoran`.`tblorder` on(`dbrestoran`.`tblpelanggan`.`idpelanggan` = `dbrestoran`.`tblorder`.`idpelanggan`))
md5=ebb9d0a328185cf833050606f1901569
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001727330627786140
create-version=2
source=SELECT tblorder.idorder, tblorder.idpelanggan, tblorder.tglorder, tblorder.total, tblorder.bayar, tblorder.kembali, tblorder.status, tblpelanggan.pelanggan, tblpelanggan.alamat, tblpelanggan.telp, tblpelanggan.email, tblpelanggan.password, tblpelanggan.aktif\nFROM tblpelanggan INNER JOIN tblorder ON tblpelanggan.idpelanggan = tblorder.idpelanggan
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `dbrestoran`.`tblorder`.`idorder` AS `idorder`,`dbrestoran`.`tblorder`.`idpelanggan` AS `idpelanggan`,`dbrestoran`.`tblorder`.`tglorder` AS `tglorder`,`dbrestoran`.`tblorder`.`total` AS `total`,`dbrestoran`.`tblorder`.`bayar` AS `bayar`,`dbrestoran`.`tblorder`.`kembali` AS `kembali`,`dbrestoran`.`tblorder`.`status` AS `status`,`dbrestoran`.`tblpelanggan`.`pelanggan` AS `pelanggan`,`dbrestoran`.`tblpelanggan`.`alamat` AS `alamat`,`dbrestoran`.`tblpelanggan`.`telp` AS `telp`,`dbrestoran`.`tblpelanggan`.`email` AS `email`,`dbrestoran`.`tblpelanggan`.`password` AS `password`,`dbrestoran`.`tblpelanggan`.`aktif` AS `aktif` from (`dbrestoran`.`tblpelanggan` join `dbrestoran`.`tblorder` on(`dbrestoran`.`tblpelanggan`.`idpelanggan` = `dbrestoran`.`tblorder`.`idpelanggan`))
mariadb-version=100432
