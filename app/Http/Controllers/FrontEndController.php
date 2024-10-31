<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Theme;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use App\Models\LanguageCombo;
use App\Models\Order;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use GoogleDriveAdapter;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;


class FrontEndController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            if(Auth::user()->roles == 1 || Auth::user()->roles == 3){
                return redirect('/translator/translator-bin');
            }
            if(Auth::user()->roles == 2){
                return redirect('/editor/editor-bin');
            }
        }
        $array = array('test' => 'test');
        return Theme::uses('default')->layout('loginlayout')->of('frontend.login', $array)->render();
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
      }
    public function register()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->layout('loginlayout')->of('frontend.register', $array)->render();
    }
    public function email_password_reset($email)
    {
        $encrypted_email = $email;
        $decrypted_email = pack("H*",$encrypted_email);
        $position = strpos($decrypted_email, '/');
        $decrypted_email = substr($decrypted_email, 0, $position);
        $data = array('email' => $decrypted_email );
        
        return Theme::uses('default')->layout('loginlayout')->of('emails.reset_password', $data)->render();
    }
    public function index()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->of('frontend.index', $array)->render();
    }
    public function faq()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->of('frontend.faq', $array)->render();
    }
    public function terms()
    {
        $array = array();
        return Theme::uses('default')->of('frontend.terms', $array)->render();
    }
    public function select_billing()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.select-billing', $array)->render();
    }
    public function ntd()
    {
        $array = array('languages' => 'test' );
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.ntd', $array)->render();
    }
    public function usd(Request $request)
    {
        $array = array('languages' => 'test' );
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.usd', $array)->render();
    }

    public function order_quote(Request $request)
    {
        if($request->order_number)
        {
            $order =  Order::where('order_number', $request->order_number)->first();
            if($order && $order->overseas == 0)
            {
                if($order->payment_status == 1)
                    return redirect('new-order/success/'.$request->order_number);

                $docs = json_decode($order->associated_docs);
                $word_count = 0;
                foreach($docs as $k => $v)
                {
                    $word_count += $v->word_count;
                }
                $order_time = $order->hours;
    
                $langs =  Language::all();
                $lang = [];
                foreach($langs as $k => $v)
                {
                    $lang[$v->code] = $v->{'name_'.App()->getLocale()};
                }
                $order->lang = $lang;
                $order->order_time = $order_time;

                return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.quote-ntd', $order)->render();
            }
            if($order && $order->overseas == 1)
            {
                $docs = json_decode($order->associated_docs);
                $word_count = 0;
                foreach($docs as $k => $v)
                {
                    $word_count += $v->word_count;
                }
                $order_time = $order->hours;
    
                $langs =  Language::all();
                $lang = [];
                foreach($langs as $k => $v)
                {
                    $lang[$v->code] = $v->{'name_'.App()->getLocale()};
                }
                $order->lang = $lang;
                $order->order_time = $order_time;
                
                return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.quote-usd', $order)->render();
            }
            return abort(404);
        }
        else
            return abort(404);
    }
    
    public function show_quote(Request $request)
    {
        $order = [];
        if($request->order_number)
        {
            $order =  Order::where('order_number', $request->order_number)->first();
            if($order)
            {
                $langs =  LanguageCombo::all();
                $combo = [];
                foreach($langs as $k => $v)
                {
                    $combo[$v->code] = $v->from->{'name_'.App()->getLocale()}.' => '.$v->to->{'name_'.App()->getLocale()};
                }
                $order->combo = $combo;
            }
            else
                return abort(404);
        }
        
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.view-quote', $order)->render();
    }

    public function order_success(Request $request)
    {
        if($request->order_number)
        {
            $order =  Order::where([['order_number', $request->order_number], ['payment_status', 1]])->orWhere([['order_number', $request->order_number], ['payment_type', 2]])->first();
            if($order)
            {
                $docs = json_decode($order->associated_docs);
                $word_count = 0;
                foreach($docs as $k => $v)
                {
                    $word_count += $v->word_count;
                }
                $order_time = $order->hours;
    
                $langs =  Language::all();
                $lang = [];
                foreach($langs as $k => $v)
                {
                    $lang[$v->code] = $v->{'name_'.App()->getLocale()};
                }
                $order->lang = $lang;
                $order->order_time = $order_time;
            }
            else
                return abort(404);
        }
        else
            return abort(404);
        
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.success-order', $order)->render();
    }

    public function order_failed()
    {
        $array = array();
        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.failed-order', $array)->render();
    }

    public function invoice(Request $request)
    {
        $array = array();
        $order = Order::where('email', $request->email)->orderBy('id', 'desc')->first();
        if($order)
            $array = $order;

        return Theme::uses('default')->layout('orderlayout')->of('frontend.new-order.invoice', $array)->render();
    }
    
    public function my_languages()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->of('frontend.dashboard.my-languages', $array)->render();
    }
    public function change_password()
    {
        $array = array('test' => 'test' );
        return Theme::uses('default')->layout('loginlayout')->of('frontend.dashboard.change-password', $array)->render();
    }

    public function getLanguages(Request $request){
        $languages = Language::all();
        foreach($languages as $k => $v)
        {
            $langs[$v->code] = $v->{'name_'.App()->getLocale()};
        }

        $billingCurrency = $request->header('billing');
        if ($billingCurrency == 'ntd') {
            $combos = LanguageCombo::where('activated', 1)->get();
        } else {
            $combos = LanguageCombo::where('activated_us', 1)->get();
        }
        foreach($combos as $k => $v)
        {
            $from[$v->language_from] = $langs[$v->language_from];
        }
        $data = [
            'code' => array_keys($from),
            'value' => array_values($from),
        ];
        
        return response()->json($data);
    }

    public function getLanguagesTo(Request $request){
        $languages = Language::all();
        foreach($languages as $k => $v)
        {
            $langs[$v->code] = $v->{'name_'.App()->getLocale()};
        }

        $billingCurrency = $request->header('billing');
        if ($billingCurrency == 'ntd') {
            $combos = LanguageCombo::where([['language_from', $request->code], ['activated', 1]])->get();
        } else {
            $combos = LanguageCombo::where([['language_from', $request->code], ['activated_us', 1]])->get();
        }
        foreach($combos as $k => $v)
        {
            $to[$v->language_to] = $langs[$v->language_to];
        }
        $data = [
            'code' => array_keys($to),
            'value' => array_values($to),
        ];
        
        return response()->json($data);
    }

    public function getlanguageComboByCode(Request $request){
        $langcombo = LanguageCombo::where('code', $request['language_combo'])->first();

        if($langcombo){
            return $langcombo;
        }
        else{
            return false;
        }
    }

    public function uploadFile(Request $request) {
        $file = $request->file('file');

         //Move Uploaded File
        $destinationPath = base_path('public/storage/files');
        $originalName = $file->getClientOriginalName();
        $newFileName = $this->randomPrefix(20).".".$file->getClientOriginalExtension();

        // $moveFile = $file->move($destinationPath, $file->getClientOriginalName());

        $moveFile = Storage::disk('public')->url($request->file('file')->store('files', 'public'));
        
        if($moveFile){
            $uploaded_file_parts = pathinfo($moveFile);
            $uploadFile = $destinationPath . '/'.$uploaded_file_parts['filename'] . '.' . $uploaded_file_parts['extension'];
            $wordCount = 0;
            // print_r(mime_content_type($uploadFile));die;
            $mimetype = mime_content_type($uploadFile);
            if($mimetype === 'application/msword' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimetype === 'application/octet-stream' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document'){
                if($mimetype === 'application/msword'){
                    $fileHandle = fopen($uploadFile, "r");
                    $text = shell_exec(env('Antiword').' -m UTF-8.txt ' . $uploadFile);
                    if($text == "\n"){
                       print_r("I'm afraid the text stream of this file is too small to handle.");die;
                       $text = shell_exec(env('Antiword').' '.$uploadFile);
                    }
                    $striped_content= $text;
                }
                if ($mimetype === 'application/octet-stream' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document'){

                    $striped_content = '';
                    $content = '';
                    $zip = new ZipArchive;
                    if (true === $zip->open($uploadFile)) {
                        // If done, search for the data file in the archive
                        if (($index = $zip->locateName("word/document.xml")) !== false) {
                            // If found, read it to the string
                            $data = $zip->getFromIndex($index);
                            $zip->close();
                            $xml = new \DOMDocument();
                            $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                            $striped_content = strip_tags($xml->saveXML());
                            $m = mb_strlen($striped_content,'utf-8');
                            $s = strlen($striped_content);
                            $striped_content = strval($striped_content);
                        }
                    }
                }

                $wordCount = $this->comment_count_word($striped_content);
                
                $file_name = $request->file('file')->getClientOriginalName();
                Storage::disk('google')->put($file_name, file_get_contents($uploadFile));
                
                $path = Storage::disk('google')->url($request->file('file')->getClientOriginalName());
                
            }
            if(substr($uploadFile, -3) == "txt"){
                $text = file_get_contents($uploadFile);
                $text_array = explode(PHP_EOL,$text);
                $wordCount = $this->comment_count_word($text);
                
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                /* Note: any element you append to a document must reside inside of a Section. */

                // Adding an empty Section to the document...
                $section = $phpWord->addSection();
                // Adding Text element to the Section having font styled by default...
                    foreach($text_array as $txt){
                    $section->addText($txt);
                    $section->addTextBreak();
                }
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                ob_start();
                $objWriter->save('php://output');
                $content = ob_get_contents();
                ob_end_clean();
                $file_name = $request->file('file')->getClientOriginalName().'.docx';
                Storage::disk('google')->put($file_name, $content);
                $path = Storage::disk('google')->url($request->file('file')->getClientOriginalName().'.docx');

            }
            // print_r($request->file('file'));die;
            $file = json_decode($request->file('file'));
            
            $disk = Storage::disk('google');
            $id = $disk->getMetadata($file_name)['path'];

            $client = new Google_Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));
            $service = new Google_Service_Drive($client);
            $permission = new Google_Service_Drive_Permission();
            $permission->setRole('reader');
            $permission->setType('anyone');
            
            $permissions_id = $service->permissions->create($id, $permission);

            $array = array([
                'file_name' => $originalName,
                'file' => $request->file('file'),
                'word_count' => $wordCount,
                'id' => $id,
                'path' => $path
            ]);
            return $array;
        }
    }
    
    static public function comment_count_word($text){
		$encoding = mb_detect_encoding($text);

        $result = array(
            'count_zh' => 0,
            'count_en' => 0,
            'count_jp' => 0,
            'count_es' => 0,
            'count_id' => 0,
            'count_ko' => 0,
        );

		$text_zh  = preg_replace("/[^\p{Han}\？\！\；\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text);
        $result['count_zh'] =  mb_strlen($text_zh, $encoding);
        
		$text_en  = preg_replace("/[\'\"]/","", $text);
		$text_en  = preg_replace("/[^a-zA-Z\s]/"," ", $text_en);
		$result['count_en'] = str_word_count($text_en);
        
        $text_cyrillic  = preg_replace("/[^\p{Cyrillic}\s]/","", $text);
        $result['count_ru'] = str_word_count($text_cyrillic);

		//$pattern_jp = "[^\p{Hiragana}\p{Katakana}\]";
		$text_jp  = preg_replace("/[^\p{Han}\p{Hiragana}\p{Katakana}\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text );
		//$this->count_jp = mb_strlen($text_jp, $encoding);
		$result['count_jp'] =  mb_strlen($text_jp, $encoding);
        
        $text_ko  = preg_replace("/[^\p{Han}\p{Hangul}\？\！\；\．\〜\ー\。\，\「\」\《\、\》\【\】\『\』\：\（\）\（\）\／\・]/u","", $text);
		$result['count_ko'] =  mb_strlen($text_ko, $encoding);
        
        $text_ru  = preg_replace("/[^\x{0430}-\x{044F}\x{0410}-\x{042F}\s]/u"," ", $text);
        $result['count_ru'] = count(preg_split('/\s+/', $text_ru));
        
        $result['count_ru_literra'] = $result['count_ru'];
        
        $result['count_es'] = str_word_count($text_en);
        
        $result['count_id'] = str_word_count($text_en);

        return $result;
	}

    //generate random char
    function randomPrefix($length){
        $random= "";
        srand((double)microtime()*1000000);
    
        $data = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    
        for($i = 0; $i < $length; $i++){
            $random .= substr($data, (rand()%(strlen($data))), 1);
        }
    
        return $random;
    }

}
