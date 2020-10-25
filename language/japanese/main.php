<?php
declare(strict_types=1);

define('_MD_PDF_A', 'PDFアップロード');
define('_MD_PDF_A_DESC', ' ファイル名を「PMID.pdf」としてアップロードすることにより登録出来ます。');
define('_MD_PDF_B', 'PMIDでの登録');
define('_MD_PDF_B_DESC', ' PDFファイルがなくてもここでPMIDを入力して送信すれば登録出来ます。<br>ファイルの追加アップロードは「PDF upload」で可能です。');
define('_MD_PDF_C', '一括登録（アップロード済みのデータが対象）');
define('_MD_PDF_C_DESC', ' アップロード済みのデータを一括で登録出来ます。<br>数によってはかなり時間がかかるので気長にお待ち下さい。');
define('_MD_PDF_D', '一括登録（PMIDのみ登録したいデータが対象）');
define('_MD_PDF_D_DESC', ' エクスポートで作成したデータを元にPMIDのみ一括登録出来ます。');
define('_MD_PDF_E', 'リストエクスポート');
define('_MD_PDF_E_DESC', '登録されてあるデータのリストを取得することが出来ます。<br>このファイルを上の一括登録２で指定して登録して下さい。');
define('_MD_L1', 'PDFがないデータ');
define('_MD_L2', 'PDFがあるデータ');
define('_MD_L3', '全て');
define('_MD_L4', '自分の登録したデータのみ');
define('_MD_PDF_F', 'データ消去');
define('_MD_PDF_F_DESC', '間違って登録したデータはここにPMIDを入力すれば消去することが出来ます。');

define('_MD_WO_A', '論文情報');
define('_MD_JOUR_REG', 'ジャーナル/書籍　新規登録');
define('_MD_JOUR_EDIT', 'ジャーナル/書籍　修正');
define('_MD_UPLOAD_INFO', 'PDFファイルのアップロード');
define('_MD_REMOVE_INFO', '論文情報を消去する');
define('_MD_EDIT_INFO', '論文情報を修正する');
define('_MD_REGISTER_AUTO', '自動登録');
define('_MD_REGISTER_AUTOINFO', '"uploads_xml" ディレクトリにあるXMLファイルを利用して自動登録を行う');
define('_MD_REG_RESULT', '登録結果');

define('_MD_XML_DOWNLOAD', 'XMLアーカイブのダウンロード');
define('_MD_XML_DOWNLOAD_DESC', '論文データのXMLファイルが入っています。');

define('_MD_FAVORITE_MKDIR', '新規ディレクトリ');
define('_MD_FAVORITE_DIRLIST', 'ディレクトリ一覧');
define('_MD_FAVORITE_DIRTOP', 'トップディレクトリ');

define('_MD_NOTE', 'メモ一覧');
define('_MD_NOTE_MES', 'メッセージ');
define('_MD_NOTE_PRI', '非公開');
define('_MD_NOTE_PUB', '公開');
define('_MD_NOTE_NEW', '新規');
define('_MD_NOTE_NEW2', '新規登録');
define('_MD_NOTE_CLOSE', '閉じる');
define('_MD_NOTE_ADDED', '上記のメモが登録されました。');
define('_MD_NOTE_EDIT', 'メモ編集');
define('_MD_NOTE_EDITED', 'メモが変更されました。');
define('_MD_NOTE_EDIT_D', '排除');
define('_MD_NOTE_EDIT_C', '編集');
define('_MD_NOTE_PERMISSION_ERROR', 'このメモを編集する権限がありません。');
define('_MD_NOTE_NODATA_ERROR', 'あなたの指定したメモは存在しません。');
define('_MD_NOTE_DEL', 'このメモを排除してよろしいですか？');
define('_MD_NOTE_DELETED', '排除されました。');
define('_MD_NOTE_ATTACHED', '添付ファイル');
define('_MD_NOTE_ATTACHED_EDIT', '添付変更');
define('_MD_NOTE_ATTACHED_DEL', '添付ファイル排除');
define('_MD_NOTE_ATTACHED_DELETED', '添付ファイルは排除されました。');
define('_MD_NOTE_ATTACHED_ADDED', 'ファイルがアップロードされました。');
define('_MD_NOTEADDED', 'メモを追加しました。');
define('_MD_ATTACHERROR', 'ファイル添付に失敗しました。');
define('_MD_SUFFIXERROR', 'この拡張子は許可されていません。');

define('_MD_FAVORITE', 'お気に入り');
define('_MD_FAVO_ADD', 'お気に入り登録');
define('_MD_FAVO_WHICHD', 'どのディレクトリに登録しますか？');
define('_MD_FAVO_ADDED', 'お気に入りに登録されました。');
define('_MD_FAVORITE_PUB', 'このディレクトリを公開する');
define('_MD_FAVORITE_PRI', 'このディレクトリを非公開する');
define('_MD_FAVORITE_FREE', '解除');
define('_MD_FAVORITE_ALL', '全データ');

define('_MD_CHECK_ALL', '全て選択');
define('_MD_DELETE', '排除');
define('_MD_INSERTINTO', 'へ移動');
define('_MD_ADD_BIBLIO', '参考文献リストに追加');

define('_MD_BIBLIO', '参考文献リスト作成');
define('_MD_BIBLIO_EXIST', '件のデータが参考文献リストにあります');
define('_MD_BIBLIO_EXPORT', '出力する');
define('_MD_BIBLIO_NEW', '新規テンプレート');
define('_MD_BIBLIO_MODIFY', 'テンプレート修正');
define('_MD_BIBLIO_SELECT', 'テンプレート選択');
define('_MD_BIBLIO_MAKENEW', '新規テンプレートを作成する');
define('_MD_BIBLIO_SHOW', '参考文献リスト');
define('_MD_BIBLIO_TEMPMADE', 'テンプレートを作成しました。');
define('_MD_BIBLIO_TEMPMADEERROR', 'テンプレート作成失敗しました。');
define('_MD_BIBLIO_TEMPEDIT', 'テンプレートを編集しました。');
define('_MD_BIBLIO_TEMPEDITERROR', 'テンプレート編集失敗しました。');
define('_MD_DELTEMPLATE', 'このテンプレートを排除する場合はチェック');

define('_MD_PubMedSearch', 'PubMed 検索');
define('_MD_PubMedSearch_unin_submit', 'チェックしたデータを隠す or 表示する');
define('_MD_PubMedSearch_regist_submit', 'チェックしたデータを登録する');
define('_MD_PubMedEditkeyword', 'キーワード編集');
define('_MD_PubMedCreatekeyword', '新規キーワード登録');
define('_MD_PubMedreturn', 'PubMed 検索に戻る');
define('_MD_PubMedmanage', '登録キーワード管理');
define('_MD_MKEYDEL', 'Title, Keywordを空白にして送信するとデータが排除されます.');

define('_MD_SCUT', 'ショートカット');
define('_MD_SCUTMADE', 'ショートカットを作成しました。');
define('_MD_SCUTDEL', 'ショートカットを排除しました。');

define('_MD_DESCRIPTION', '詳細');
define('_MD_DOSENTEXIST', '指定されたデータは存在しません。');
define('_MD_NOTPERMITTED', 'アクセス権がありません。');
define('_MD_WRONGACCESS', '不正アクセス');

define('_MD_AUTHOR', '著者');
define('_MD_YEAR', '発行年');
define('_MD_JB', 'ジャーナル/書籍');
define('_MD_TITLE', 'タイトル');
define('_MD_INFO', '登録情報');
define('_MD_ABST', '概要');
define('_MD_JP', '日本語');
define('_MD_VP', '巻/ページ');

define('_MD_EDITINFO', '論文情報を編集する');
define('_MD_FAVO_ALREADY', 'このデータは既に登録されています。');
define('_MD_TXTONLY', '使用できるのはテキスト形式のファイルのみです。');
define('_MD_REGISTERED', '登録しました。');
define('_MD_REGISTEREDERROR', '登録失敗しました。');
define('_MD_DATA_DELETED', 'を排除しました。');
define('_MD_DATA_DELETEDERROR', '排除失敗しました。');
define('_MD_CHANGEPDF', '差し替え');
define('_MD_DATA_EDITERROR', '変更失敗しました。');
define('_MD_DEL_DATA', '文献情報を排除しました。<br>');
define('_MD_DEL_DATA2', '文献情報排除に失敗しました。<br>');
define('_MD_DEL_PDF', 'PDFを排除しました。<br>');
define('_MD_DEL_PDF2', 'PDF排除に失敗しました。<br>');
define('_MD_DEL_XML', 'XMLを排除しました。<br>');
define('_MD_DEL_XML2', 'XML排除に失敗しました。<br>');
define('_MD_CONFLICT', '既に登録されているXMLと名前が重複するため変更できません。');
define('_MD_EDITED', '修正しました.<br>');
define('_MD_DELXMLERROR', '古いXMLの排除に失敗しました.<br>');
define('_MD_GENERATEXML', 'XMLを生成しました.<br>');
define('_MD_RENAMEPDFERROR', 'PDFのリネームに失敗しました.<br>');
define('_MD_RENAMEPDF', 'PDFをリネームしました.<br>');
define('_MD_UPLOADERROR', 'アップロード失敗しました.');
define('_MD_UPLOADERROR2', 'アップロードできるのはPDFファイルのみです。');
define('_MD_UPLOADED', 'PDFをアップロードしました。');
define('_MD_DELETEDJ', 'ジャーナルを排除しました。');
define('_MD_DELETEDJERROR', 'ジャーナルを排除に失敗しました。');
