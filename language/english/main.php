<?php
declare(strict_types=1);

define('_MD_PDF_A', 'Upload PDF file');
define('_MD_PDF_A_DESC', '	Register by uploading PDF file which is named "PMID.pdf"');
define('_MD_PDF_B', 'Submit PMID only for later uploading');
define('_MD_PDF_B_DESC', 'Register by entering PMID<br>Even after registration, PDF file can be uploaded by "Upload PDF file"');
define('_MD_PDF_C', 'Automatic registraion');
define('_MD_PDF_C_DESC', 'Automatic registration by PDF files in the pdf file directory <br>It will require considerable time.');
define('_MD_PDF_D', 'Automatic registration');
define('_MD_PDF_D_DESC', 'Automatic registration by PMID list file (csv format)');
define('_MD_PDF_E', 'Export registered PMID list');
define('_MD_PDF_E_DESC', 'Export registered PMID list in csv format<br>This file can be used in "Automatic registration (2)"');
define('_MD_L1', 'without PDF file');
define('_MD_L2', 'with PDF file');
define('_MD_L3', 'all');
define('_MD_L4', 'only data which were registered by you.');
define('_MD_PDF_F', 'Remove');
define('_MD_PDF_F_DESC', 'Remove PMID registration');

define('_MD_WO_A', 'Bibliographic information');
define('_MD_JOUR_REG', 'Journal/Book Registration');
define('_MD_JOUR_EDIT', 'Journal/Book edit');
define('_MD_UPLOAD_INFO', 'Upload PDF file');
define('_MD_REMOVE_INFO', 'Remove registered information');
define('_MD_EDIT_INFO', 'Edit registered information');
define('_MD_REGISTER_AUTO', 'Automatic registration');
define('_MD_REGISTER_AUTOINFO', 'Registration by XML files in "uploads_xml" directory');
define('_MD_REG_RESULT', 'Registration result');

define('_MD_XML_DOWNLOAD', 'Download XML archive');
define('_MD_XML_DOWNLOAD_DESC', 'You can get XML files.');

define('_MD_FAVORITE_MKDIR', 'New Directory');
define('_MD_FAVORITE_DIRLIST', 'Directory List');
define('_MD_FAVORITE_DIRTOP', 'Directory Top');

define('_MD_NOTE', 'Note list');
define('_MD_NOTE_MES', 'Message');
define('_MD_NOTE_PRI', 'private');
define('_MD_NOTE_PUB', 'public');
define('_MD_NOTE_NEW', 'new');
define('_MD_NOTE_NEW2', 'add new note');
define('_MD_NOTE_CLOSE', 'close');
define('_MD_NOTE_ADDED', 'Above note has added.');
define('_MD_NOTE_EDIT', 'Note Edit');
define('_MD_NOTE_EDITED', 'Above note has changed.');
define('_MD_NOTE_EDIT_D', 'delete');
define('_MD_NOTE_EDIT_C', 'edit');
define('_MD_NOTE_PERMISSION_ERROR', "You don't have the permission change this note.");
define('_MD_NOTE_NODATA_ERROR', "Such data doesn't exist.");
define('_MD_NOTE_DEL', 'Do you delete this note?');
define('_MD_NOTE_DELETED', 'The note has deleted.');
define('_MD_NOTE_ATTACHED', 'File attachment');
define('_MD_NOTE_ATTACHED_EDIT', 'Attachment');
define('_MD_NOTE_ATTACHED_DEL', 'Delete attachment');
define('_MD_NOTE_ATTACHED_DELETED', 'Attachment was deleted.');
define('_MD_NOTE_ATTACHED_ADDED', 'Attachment was uploaded.');
define('_MD_NOTEADDED', 'Note was added.');
define('_MD_ATTACHERROR', "Error: Attachment didn't be uploaded.");
define('_MD_SUFFIXERROR', "Error: This suffix doesn't be permitted.");

define('_MD_FAVORITE', 'Favorite');
define('_MD_FAVO_ADD', 'Add Favorite');
define('_MD_FAVO_WHICHD', 'Which directory do you want to add this data?');
define('_MD_FAVO_ADDED', 'This data has added your favorite list.');
define('_MD_FAVORITE_PUB', 'Make this dir public');
define('_MD_FAVORITE_PRI', 'Make this dir private');
define('_MD_FAVORITE_FREE', 'Release');
define('_MD_FAVORITE_ALL', 'All data');

define('_MD_CHECK_ALL', 'Check All');
define('_MD_DELETE', 'Delete');
define('_MD_INSERTINTO', 'insert into');
define('_MD_ADD_BIBLIO', 'Add your bibliography list');

define('_MD_BIBLIO', 'Making Bibliography List');
define('_MD_BIBLIO_EXIST', 'data exist on your bibliograpy list');
define('_MD_BIBLIO_EXPORT', 'show bibliograpy list');
define('_MD_BIBLIO_NEW', 'New template');
define('_MD_BIBLIO_MODIFY', 'Modify template');
define('_MD_BIBLIO_SELECT', 'Select template');
define('_MD_BIBLIO_MAKENEW', 'Making new template');
define('_MD_BIBLIO_SHOW', 'Show bibliography list');
define('_MD_BIBLIO_TEMPMADE', 'Template was made.');
define('_MD_BIBLIO_TEMPMADEERROR', "Error: Template didn't be made.");
define('_MD_BIBLIO_TEMPEDIT', 'Template was edited.');
define('_MD_BIBLIO_TEMPEDITERROR', "Error: Template didn't be edited.");
define('_MD_DELTEMPLATE', 'Check when you delete this template.');

define('_MD_PubMedSearch', 'PubMed Search');
define('_MD_PubMedSearch_unin_submit', 'Hide or show checked data.');
define('_MD_PubMedSearch_regist_submit', 'Register checked data.');
define('_MD_PubMedEditkeyword', 'Edit Keyword');
define('_MD_PubMedCreatekeyword', 'Register Keyword');
define('_MD_PubMedreturn', 'Go back to the PubMed Search');
define('_MD_PubMedmanage', 'Keyword Management');
define('_MD_MKEYDEL', 'Make forms empty and click submit when you delete keyword.');

define('_MD_SCUT', 'Shortcut');
define('_MD_SCUTMADE', 'Shortcut was made.');
define('_MD_SCUTDEL', 'Shortcut was deleted.');

define('_MD_DESCRIPTION', 'Detail');
define('_MD_DOSENTEXIST', "The data doesn't exist.");
define('_MD_NOTPERMITTED', "You don't have the permission to access.");
define('_MD_WRONGACCESS', 'Unauthorized Access');

define('_MD_AUTHOR', 'Author');
define('_MD_YEAR', 'Year');
define('_MD_JB', 'Journal/Book');
define('_MD_TITLE', 'Title');
define('_MD_INFO', 'Registered by');
define('_MD_ABST', 'Abstract');
define('_MD_JP', 'other language');
define('_MD_VP', 'vol/pp');

define('_MD_EDITINFO', 'Edit this infomation');
define('_MD_FAVO_ALREADY', 'This data is already exists in your favorite.');
define('_MD_TXTONLY', 'You can only use [.txt] file.');
define('_MD_REGISTERED', 'The data was registered.');
define('_MD_REGISTEREDERROR', "Error: The data didn't be registered.");
define('_MD_DATA_DELETED', ' was deleted.');
define('_MD_DATA_DELETEDERROR', 'Error: fail to delete');
define('_MD_CHANGEPDF', 'Change PDF file');
define('_MD_DATA_EDITERROR', 'Error: fail to change');
define('_MD_DEL_DATA', 'The data was deleted.<br>');
define('_MD_DEL_DATA2', 'Error: fail to delete the data.<br>');
define('_MD_DEL_PDF', 'PDF was deleted.<br>');
define('_MD_DEL_PDF2', "Error: PDF didn't be deleted.<br>");
define('_MD_DEL_XML', 'XML was deleted.<br>');
define('_MD_DEL_XML2', "Error: XML didn't be deleted.<br>");
define('_MD_CONFLICT', 'Error: Conflict with already registered data.');
define('_MD_EDITED', 'edited<br>');
define('_MD_DELXMLERROR', "Error: Old XML didn't be deleted.<br>");
define('_MD_GENERATEXML', 'XML was made.<br>');
define('_MD_RENAMEPDFERROR', "Error: PDF didnt't be renamed.<br>");
define('_MD_RENAMEPDF', 'PDF was renamed.<br>');
define('_MD_UPLOADERROR', 'Error: fail to upload');
define('_MD_UPLOADERROR2', 'You can upload [.pdf] file only.');
define('_MD_UPLOADED', 'PDF was uploaded.');
define('_MD_DELETEDJ', 'Journal was deleted.');
define('_MD_DELETEDJERROR', "Error: Journal didn't be deleted.");
