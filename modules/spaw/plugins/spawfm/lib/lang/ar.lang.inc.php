<?php 
// ================================================
// SPAW File Manager plugin
// ================================================
//
//
// Arabic language file
// Traslated: Mohammed Ahmed
// Gaza, Palestine
// http://www.maaking.com
// Email/MSN: m@maaking.com
//
// last update: 18-oct-2007
//
// ================================================
// Developed: Saulius Okunevicius, saulius@solmetra.com
// Copyright: Solmetra (c)2006 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.2.0
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-1256';

// text direction for the language
$spaw_lang_direction = 'rtl';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'spawfm' => array(
    'title' => '����� �������',
    'error_reading_dir' => '���: �� ���� ����� ������� ������',
    'error_upload_forbidden' => '���: ����� ������� ��� ����� �� ��� ������.',
    'error_upload_file_too_big' => '�� �������: ��� ����� ���� ���..',
    'error_upload_failed' => '�� ����� �����.',
    'error_upload_file_incomplete' => '�� �� ����� ����ݡ ������ �������� ��� ����.',
    'error_bad_filetype' => '���: ��� ����� �� ������� ��� ����� ��.',
    'error_max_filesize' => '����� ������� ��: ',
    'error_delete_forbidden' => '���: ��� ������� �� ��� ������ ��� ����� ��.',
    'confirm_delete' => '�� ���� ��� ����� "[*file*]"?',
    'error_delete_failed' => '���: ���� �� ���� �� ������� ���� ��� �����.',
    'error_no_directory_available' => '�� ���� ������ �������.',
    'download_file' => '[����� �����]',
    'error_chmod_uploaded_file' => '�� ��� ����ݡ ��� �� ���� ����� CHMOD',
    'error_img_width_max' => '���� ��� ����� �� �� : [*MAXWIDTH*]px',
    'error_img_height_max' => '���� ������ ����� �� �� : [*MAXHEIGHT*]px',
    'rename_text' => '���� ����� ������ ��  "[*FILE*]":',
    'error_rename_file_missing' => '���� ����� ������ɡ �� ��� ������ ��� �����.',
    'error_rename_directories_forbidden' => '���: ����� ������� ��� ������ ���� ������.',
    'error_rename_forbidden' => '���: ����� ������� ��� ������ ���� ������.',
    'error_rename_file_exists' => '���: "[*FILE*]" ����� �����.',
    'error_rename_failed' => '���: ���� ����� �������. ',
    'error_rename_extension_changed' => '���: �� ���� ����� �������!',
    'newdirectory_text' => '���� ��� ������:',
    'error_create_directories_forbidden' => '���: �� ���� ������ ������ ����',
    'error_create_directories_name_used' => '���: ��� ����� ����� �� ���.',
    'error_create_directories_failed' => '������ ����� �����ϡ �� ���� �������.',
    'error_create_directories_name_invalid' => '�� ���� ������� ����� ������� �� �����: / \\ : * ? " < > |',
    'confirmdeletedir_text' => '�� ���� ��� ������:  "[*DIR*]"?',
    'error_delete_subdirectories_forbidden' => '������ ��� ��� ������.',
    'error_delete_subdirectories_failed' => '�� ���� ��� ������. ���� ��� ���� ������� ���� ���.',
    'error_delete_subdirectories_not_empty' => '������ ��� ����.',
  ),
  'buttons' => array(
    'ok'        => '  �����  ',
    'cancel'    => '�����',
    'view_list' => '���� �����: �����',
    'view_details' => '���� �����: ������',
    'view_thumbs' => '���� �����: ������',
    'rename'    => '����� ����� ... ',
    'delete'    => '���',
    'go_up'     => '�����',
    'upload'    =>  '��� �����',
    'create_directory'  =>  '����� ����',
  ),
  'file_details' => array(
    'name'  =>  '���',
    'type'  =>  '���',
    'size'  =>  '���',
    'date'  =>  '����� �������',
    'filetype_suffix'  =>  '���',
    'img_dimensions'  =>  '�������',
    'file_folder'  =>  '��� ����',
  ),
  'filetypes' => array(
    'any'       => '���� ������� (*.*)',
    'images'    => '���',
    'flash'     => '����',
    'documents' => '�����',
    'audio'     => '���',
    'video'     => '�����',
    'archives'  => '�����',
    '.jpg'  =>  '���� JPG ',
    '.jpeg'  =>  '���� JPG ',
    '.gif'  =>  '���� GIF ',
    '.png'  =>  '���� PNG ',
    '.swf'  =>  '��� ���� Flash movie',
    '.doc'  =>  '����� Microsoft Word',
    '.xls'  =>  '���� Microsoft Excel ',
    '.pdf'  =>  '����� PDF document',
    '.rtf'  =>  '����� RTF document',
    '.odt'  =>  '��� OpenDocument Text',
    '.ods'  =>  '��� OpenDocument Spreadsheet',
    '.sxw'  =>  '��� 1 OpenOffice.org 1.0 Text Document',
    '.sxc'  =>  '���1  OpenOffice.org 1.0 Spreadsheet',
    '.wav'  =>  '��� WAV audio file',
    '.mp3'  =>  '��� MP3 audio file',
    '.ogg'  =>  '��� Ogg Vorbis audio file',
    '.wma'  =>  '��� Windows audio file',
    '.avi'  =>  '���� AVI video file',
    '.mpg'  =>  '���� MPEG video file',
    '.mpeg'  =>  '���� MPEG video file',
    '.mov'  =>  '���� QuickTime video file',
    '.wmv'  =>  '���� Windows video file',
    '.zip'  =>  '����� ZIP archive',
    '.rar'  =>  '����� RAR archive',
    '.gz'  =>  '����� gzip archive',
    '.txt'  =>  '��� Text Document',
    ''  =>  '',
  ),
);
?>
