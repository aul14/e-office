<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP 5
 *
 * Application System Environment (X-ASE)
 * laxono :  Rapid Development Framework (http://www.laxono.us)
 * Copyright 2011-2015.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource data_object.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author budi.lx
 * @package 
 * @subpackage	
 * @since Aug 14, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Data_object {

	var $surat_eksternal = array('surat_eksternal_id'			=> array('validate' => 'edit', 'label' => 'ID Surat', 'rule' => ''),
									'created_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_time' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'organization_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'jenis_agenda' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'agenda_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status_berkas' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'sifat_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'jenis_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_no' 					=> array('validate' => 'both', 'label' => 'Nomor Surat', 'rule' => 'trim|required'),
									'surat_tgl' 				=> array('validate' => 'both', 'label' => 'Tanggal Surat', 'rule' => 'trim|required'),
									'surat_item_lampiran' 		=> array('validate' => 'both', 'label' => 'Item Lampiran', 'rule' => 'trim|required'),
									'surat_unit_lampiran' 		=> array('validate' => 'both', 'label' => 'Unit Lampiran', 'rule' => 'trim|required'),
									'surat_tgl_masuk' 			=> array('validate' => 'both', 'label' => 'Tanggal Terima', 'rule' => 'trim|required'),
									'surat_perihal' 			=> array('validate' => 'both', 'label' => 'Perihal', 'rule' => 'trim|required'),
									'format_surat_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_ext_title' 			=> array('validate' => 'both', 'label' => 'Jabatan Asal', 'rule' => 'trim|required'),
									'surat_ext_instansi' 		=> array('validate' => 'both', 'label' => 'Instansi Asal', 'rule' => 'trim|required'),
									'surat_ext_nama' 			=> array('validate' => 'both', 'label' => 'Nama Asal', 'rule' => 'trim|required'),
									'surat_ext_alamat' 			=> array('validate' => 'both', 'label' => 'Alamat Asal', 'rule' => 'trim|required'),
									'surat_int_jabatan' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_int_unit_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_int_unit'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_int_dir'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_int_nama' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_int_kode' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'tembusan'		 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'kode_klasifikasi_arsip' 	=> array('validate' => 'both', 'label' => 'Kode Klasifikasi', 'rule' => ''),
									'pengantar' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'surat_pengantar_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'kirim_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'terima_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'baca_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'arsip_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'approval' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'catatan_pengiriman'		=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	var $_ref_instansi_eksternal = array('entry_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'organization_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_time' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_time' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'jabatan' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'nama_pejabat' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'instansi'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'phone_office' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'fax' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'address' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status' 			=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	var $referensi = array('entry_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'organization_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_time' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_time' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'jabatan' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'nama_pejabat' 		=> array('validate' => 'none', 'label' => 'type', 'rule' => 'trim|required'),
									'instansi'			=> array('validate' => 'both', 'label' => 'val', 'rule' => 'trim|required'),
									'phone_office' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'fax' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'address' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status' 			=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	var $process_notes = array('process_note_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'organization_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'function_ref_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'table'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'ref_id'	 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'flow_seq'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'flow_type' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'note'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'user_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'created_time' 		=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);

	var $surat = array('surat_id'		=> array('validate' => 'edit', 'label' => 'ID Surat', 'rule' => ''),
			'created_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'created_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_time' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'organization_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'function_ref_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_agenda' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'agenda_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status_berkas' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'sifat_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_no' 					=> array('validate' => 'both', 'label' => 'Nomor Surat', 'rule' => 'trim|required'),
			'surat_tgl' 				=> array('validate' => 'both', 'label' => 'Tanggal Surat', 'rule' => 'trim|required'),
			'surat_item_lampiran' 		=> array('validate' => 'both', 'label' => 'Item Lampiran', 'rule' => 'trim|required'),
			'surat_unit_lampiran' 		=> array('validate' => 'both', 'label' => 'Unit Lampiran', 'rule' => 'trim|required'),
			'surat_tgl_masuk' 			=> array('validate' => 'both', 'label' => 'Tanggal Terima', 'rule' => 'trim|required'),
			'surat_perihal' 			=> array('validate' => 'both', 'label' => 'Perihal', 'rule' => 'trim|required'),
			'surat_ringkasan' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'format_surat_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
				
			'surat_from_ref' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_id' 			=> array('validate' => 'both', 'label' => 'Tujuan Surat', 'rule' => 'trim|required'),
			'surat_to_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_awal' 		        => array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_akhir' 		        => array('validate' => 'none', 'label' => '', 'rule' => ''),
			
			'signed'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'tembusan'		 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'approval' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'distribusi'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kode_klasifikasi_arsip' 	=> array('validate' => 'both', 'label' => 'Kode Klasifikasi', 'rule' => ''),
			'surat_pengantar_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kirim_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'catatan_pengiriman'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'terima_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'baca_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'arsip_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	
	var $surat_tugas = array('surat_id'	=> array('validate' => 'edit', 'label' => 'ID Surat', 'rule' => ''),
			'created_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'created_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_time' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'organization_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'function_ref_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_agenda' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'agenda_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_no' 					=> array('validate' => 'both', 'label' => 'Nomor Surat', 'rule' => 'trim|required'),
			'surat_tgl' 				=> array('validate' => 'both', 'label' => 'Tanggal Surat', 'rule' => 'trim|required'),
			'surat_unit_lampiran' 		=> array('validate' => 'both', 'label' => 'Unit Lampiran', 'rule' => 'trim|required'),
			'surat_tgl_masuk' 			=> array('validate' => 'both', 'label' => 'Tanggal Terima', 'rule' => 'trim|required'),
			'surat_perihal' 			=> array('validate' => 'both', 'label' => 'Perihal', 'rule' => ''),
			'surat_ringkasan' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'format_surat_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			
			'surat_from_ref' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_awal' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			
			'signed'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'tembusan'		 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'approval' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'distribusi'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kode_klasifikasi_arsip' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_pengantar_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kirim_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'catatan_pengiriman'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'terima_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'baca_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'arsip_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	var $ekspedisi = array('ekspedisi_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'created_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'created_time'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'modified_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'modified_time'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'organization_id'	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'jenis_agenda' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'status'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'to_ref'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'to_ref_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'to_ref_data'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'tembusan'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'pengiriman_time'	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'catatan_pengirim'	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'petugas_pengirim'	=> array('validate' => 'both', 'label' => 'Petugas Pengiriman', 'rule' => 'trim|required'),
							'penerima_time'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'catatan_penerima'	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'petugas_penerima'	=> array('validate' => 'none', 'label' => '', 'rule' => '')
			
	);
	
	var $surat_pengantar = array('pengantar_surat_eksternal_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'created_id'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'created_time'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'modified_id'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'modified_time'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'organization_id'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'status'							=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_jabatan'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_unit_id'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_unit'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_dir'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_nama'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tujuan_kode'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'tembusan'							=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'pengiriman_time'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'catatan_pengirim'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'petugas_pengirim'					=> array('validate' => 'both', 'label' => 'Petugas Pengiriman', 'rule' => 'trim|required'),
								'penerima_time'						=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'catatan_penerima'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
								'petugas_penerima'					=> array('validate' => 'none', 'label' => '', 'rule' => '')
			
	);
	
		var $disposisi = array('disposisi_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'created_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'created_time'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'modified_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'modified_time'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'organization_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'status'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'ref_type'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'ref_id'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'from_unit_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'from_user_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'from_data'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'instruksi'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'sifat'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'target_selesai'		=> array('validate' => 'both', 'label' => 'Target Selesai', 'rule' => 'trim|required'),
							'distribusi'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'to_user_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'disposisi_tgl'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'parent_id'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'complete_time'			=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	var $klasifikasi_arsip = array('entry_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'organization_id'	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'kode_klasifikasi'	=> array('validate' => 'both', 'label' => 'Kode Klasifikasi', 'rule' => 'trim|required'),
									'nama_klasifikasi'	=> array('validate' => 'both', 'label' => 'Nama Klasifikasi', 'rule' => 'trim|required'),
									'status'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'parent_id'			=> array('validate' => 'both', 'label' => 'Parent', 'rule' => 'trim|required'),
									'sort'				=> array('validate' => 'none', 'label' => '', 'rule' => '')			
	);
	
	var $organization_structure = array('organization_structure_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'created_time'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'modified_time'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'organization_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'status'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'unit_code'				=> array('validate' => 'none', 'label' => 'Kode Struktur', 'rule' => 'trim|required'),
									'unit_name'				=> array('validate' => 'both', 'label' => 'Nama Struktur', 'rule' => 'trim|required'),
									'parent_id'				=> array('validate' => 'both', 'label' => 'Parent', 'rule' => 'trim|required'),
									'level'					=> array('validate' => 'none', 'label' => 'Level', 'rule' => ''),
									'abv'					=> array('validate' => 'none', 'label' => 'Singkatan', 'rule' => ''),
									'description'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'no_surat_internal'		=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	
	var $format_surat = array('format_surat_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'function_ref_id'		=> array('validate' => 'both', 'label' => 'Fungsi', 'rule' => 'trim|required'),
							'format_title'			=> array('validate' => 'both', 'label' => 'Judul', 'rule' => 'trim|required'),
							'format_text'			=> array('validate' => 'both', 'label' => 'Format', 'rule' => 'trim|required'),
							'status'				=> array('validate' => 'none', 'label' => '', 'rule' => '')		
	);
	
	var $system_variables = array(	'config_id'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
									'key'			=> array('validate' => 'both', 'label' => 'key', 'rule' => 'trim|required'),
									'val'			=> array('validate' => 'both', 'label' => 'val', 'rule' => 'trim|required'),
									'type'			=> array('validate' => 'none', 'label' => 'type', 'rule' => 'trim|required'),
									'sort'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),	
									'active'		=> array('validate' => 'none', 'label' => '', 'rule' => '')		
	);
	
	var $surat_keputusan = array('surat_id'			=> array('validate' => 'edit', 'label' => 'ID Surat', 'rule' => ''),
			'created_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'created_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_time' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'organization_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'function_ref_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_agenda' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'agenda_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status_berkas' 			=> array('validate' => 'both', 'label' => '', 'rule' => ''),
			'sifat_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_no' 					=> array('validate' => 'both', 'label' => 'Nomor Surat', 'rule' => 'trim|required'),
			'surat_tgl' 				=> array('validate' => 'both', 'label' => 'Tanggal Surat', 'rule' => 'trim|required'),
			'surat_unit_lampiran' 		=> array('validate' => 'none', 'label' => 'Unit Lampiran', 'rule' => ''),
			
			
			'surat_tgl_masuk' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_perihal' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_ringkasan' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'format_surat_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
				
			'surat_from_ref' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			
			'signed'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'tembusan'		 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'approval' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'distribusi'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kode_klasifikasi_arsip' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_pengantar_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kirim_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'catatan_pengiriman'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'terima_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'baca_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'arsip_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	var $Kontrak = array('surat_id'			=> array('validate' => 'edit', 'label' => 'ID Surat', 'rule' => ''),
			'created_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'created_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'modified_time' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'organization_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'function_ref_id'			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_agenda' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'agenda_id' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'status_berkas' 			=> array('validate' => 'both', 'label' => '', 'rule' => 'trim|required'),
			'sifat_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'jenis_surat' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_no' 					=> array('validate' => 'both', 'label' => 'Nomor Kontrak', 'rule' => 'trim|required'),
			'surat_tgl' 				=> array('validate' => 'both', 'label' => 'Tanggal Surat', 'rule' => 'trim|required'),
			'surat_unit_lampiran' 		=> array('validate' => 'none', 'label' => 'Unit Lampiran', 'rule' => ''),
			
			
			'surat_tgl_masuk' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_perihal' 			=> array('validate' => 'none', 'label' => 'Hal', 'rule' => ''),
			'surat_ringkasan' 			=> array('validate' => 'none', 'label' => 'Biaya', 'rule' => ''),
			'format_surat_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
				
			'surat_from_ref' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_from_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_id' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_to_ref_data' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			
			'signed'					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'tembusan'		 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'approval' 					=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'distribusi'				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kode_klasifikasi_arsip' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'surat_pengantar_id' 		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'kirim_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'catatan_pengiriman'		=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'terima_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'baca_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => ''),
			'arsip_time' 				=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
	var $tujuan_surat = array('tujuan_surat_id' 	=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'function_ref_id' 		=> array('validate' => 'both', 'label' => 'Fungsi', 'rule' => 'trim|required'),
							'title' 				=> array('validate' => 'both', 'label' => 'Judul', 'rule' => 'trim|required'),
							'to_user_data' 			=> array('validate' => 'none', 'label' => '', 'rule' => ''),
							'status' 				=> array('validate' => 'none', 'label' => '', 'rule' => '')
	);
}