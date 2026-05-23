=== Multisite Shared Sidebar ===
Contributors: Mikio ISHITANI
Donate link: 
Tags: wordpress, multisite, network, sidebar, share, shared, sharing, plugins, widgets, plugin, widget, shortcode, shortcodes
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


This plugin allows a sidebar to be shared between blogs on a multisite.


== Description ==

This plugin allows a sidebar to be shared between blogs on a multisite.  
It's very simple to use.
 

1. Use the "Multisite Shared Sidebar" widget to display a sidebar from another participating sites.
 * Specifies the "Blog ID" or "Blog Path".  ( Blog ID: 1,2,3... || Blog Path: 'path' or '/path/' )
 * Specifies the "Sidebar ID".  ( Sidebar ID: 'sidebar-1' etc)  
  ※ Sidebar ID check in a theme file "functions.php", etc.  
 * "Using current sidebar defined" use it, if you check the check box. (Default)
 * If you check the "Advanced sidebar configuration" you can customize sidebar defined.  
  ※ Customizable is 'before_widget' ,  'after_widget' , 'before_title' and 'after_title'.   
  ※ Details of the parameters see register_sidebar() document.
  

2. Use the shortcode "[shared_sidebar blog='blog-id' index='sidebar-id' ]" to display a sidebar in the text region.
 * Please write shortcode on a single line.
 * To the blog attribute specifies "Blog ID" or "Blog Path".
 * To the index attribute specifies the "Sidebar ID".
 * You can customize the sidebar defined, if you specify 'advanced_config' to "sidebar_config" attribute.    
  ※ [shared_sidebar blog='blog-id' index='sidebar-id' **sidebar_config='advanced_config'** ]  
   **Customizable attribute:** ( )  
      before_widget=''  
      after_widget=''  
      before_title=''  
      after_title=''  
  ※ Details of the parameters see register_sidebar() document.  

You can also use this plugin to add sidebars that you have added to the theme yourself.

Beware, however, that after activating the plugin, you must view the dashboard of all of subdomains in the multisite before you can use the sidebar.  

= [Setup steps] =
Mr. mkormendy clearly wrote the setup step.  
Mr. mkormendy, thank you very much.  

1. in your multisite Network Admin, visit your list of sites, hover over the name of the PRIMARY site that you want to mirror a sidebar of widgets from, and note the id number at the end of the URL for that site within the your multisite wordpress installation
2. on your PRIMARY site, create a bunch of widgets in a sidebar (these will all be shared together as a group in the cloned sidebar on a SECONDARY site eventually)

3. in your theme files, where the sidebars are generated, note the id of the sidebar that you are attempting to clone

4. on your SECONDARY site, in your admin widgets section, drag and drop the [Multisite Shared Sidebar] widget to one of your sidebars available on your secondary site (if you use the same theme, you would place it in the same sidebar location as found on the primary site)

5. in the field with the title “Blog ID: or Blog Path:”, enter that ID number that you noted for the PRIMARY site in step 1.

6. in the field with the title “Sidebar ID:”, enter the name of the sidebar that you noted in step 3

7. save the widget, and load up the page of the SECONDARY site where the sidebar exists

You should see the PRIMARY sidebar duplicated inside the sidebar location of the SECONDARY site where you put the Multisite Shared Sidebar widget.


= ☆ For Japanese users ☆ =

このプラグインはマルチサイトのブログ間でサイドバーを共有します。  
使い方はとても簡単です。

1. 「Multisite Shared Sidebar」ウィジェットを使用して、別の参加サイトのサイドバーを表示します。  
 * "Blog ID" または "Blog Path" を指定します。 ( Blog ID: 1,2,3… || Blog Path: 'path' または '/path/' )
 * "sidebar id"を指定します。 ( sidebar id: 'sidebar-1' など)  
  ※ サイドバーIDはテーマファイル内の ”functions.php” などを調べてください。
 * ”現在のサイドバー定義を使用”チェックボックスをチェックすれば、それを使用します。 （デフォルト）
 * ”高度なサイドバー設定”をチェックすればサイドバー定義をカスタマイズ出来ます。  
  ※ カスタマイズ可能なのは、 'before_widget' ,  'after_widget' , 'before_title' と 'after_title' です。  
  ※ パラメーターの詳細 register_sidebar() のドキュメントを参照してください。

2. ショートコード "[shared_sidebar blog='blog-id' index='sidebar-id' ]" を使ってテキスト領域内へ表示できます.
 * 1行でショートコードを書いてください。
 * blog属性へ 'Blog ID' または 'Blog Path' を指定します。
 * index属性へ 'Sidebar ID' を指定します。
 * "sidebar_config" 属性に 'advanced_config' を指定すれば、サイドバー定義をカスタマイズ出来ます。  
  ※ [shared_sidebar blog='blog-id' index='sidebar-id' **sidebar_config='advanced_config'** ]  
   **カスタマイズ可能な属性:**  
      before_widget=''  
      after_widget=''  
      before_title=''  
      after_title=''  
  ※ パラメーターの詳細 register_sidebar() のドキュメントを参照してください。

そして、このプラグインは貴方がテーマに追加したサイドバーも共有することができます。

ただし、プラグインを activate した後、使用する前に全ての参加サイトのダッシュボードを１回は表示しなければなりません。  

= [お願い] =
* 本プラグインへの投票をお願いします。  
* 問題点やコメントなどは [Wordpress フォーラム](https://ja.forums.wordpress.org/topic/144488) へお願いします。  


== Installation ==

1. Download the plugin  
  プラグインをダウンロードします。
2. Uncompress it with your preferred unzip program  
  解凍プログラムで解凍します。
3. Copy the entire directory in your plugin directory of your WordPress blog (/wp-content/plugins)  
  ワードプレスのブログのプラグインディレクトリにディレクトリ全体をコピーします。(/wp-content/plugins)  
4. Network-activate the plugin  
  プラグインをネットワークで有効化します。
5. Please display the widgets settings page in the dashboard for all participating sites.  
  すべての参加サイトのダッシュボードでウィジェット設定ページを表示してください。


== Frequently Asked Questions ==

= This plugin can be used in single-site ? =
  No, it cannot be used. It is only the multisite.

= Can I use this plugin to Subdomain-multisite ? =
  Yes, I think it can be used.
  However, the site should be specified in the BlogID (1,2,3, ...).

== Screenshots ==

1. Registered "Text widget" to the main site "Sidebar 2".  
2. Register the "Multisite Shared Sidebar" widget to Sidebar 2 in a subsite.  
  Blog path "/" is that main site. Sidebar ID specify 'sidebar-2'.  
  "Using current sidebar defined" Have been checked. (Default)
3. If you check the "Advanced sidebar configuration", the textarea input field is displayed.
4. Shortcode example: [shared_sidebar blog='1' index='sidebar-2' ]

== Changelog ==

= 1.2 =
* I changed 'wp_get_sites()' function to' get_sites()' function by indication of Mr. mkormendy.  
* Added to the document. Mr. mkormendy clearly wrote the setup step.  
Mr. mkormendy, thank you very much.  

= 1.1.1 =
I tested this plugin on Wordpress Ver.4.2.2 and confirmed that I worked.  
本プラグインを Wordpress Ver.4.2.2 上でテストし、動作する事を確認しました。

= 1.1 =
Added the following functions.  

* When you use "Multisite Shared Sidebar" widget to sidebar definitions in current use.  
 "Multisite Shared Sidebar"ウィジェットの使用時に、現在のサイドバー定義を使用出来るようにしました。  
* You can customize the sidebar defined by advanced settings.  
  高度な設定でサイドバー定義をカスタマイズすることができます。
* Added the Japanese translation file  
  日本語翻訳ファイルを追加しました。

= 1.0 =
* First release.

== Upgrade Notice ==

No upgrade, so far.
