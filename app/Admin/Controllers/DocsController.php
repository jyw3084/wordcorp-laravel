<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Doc;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Storage;
use ZipArchive;
use PhpOffice\PhpWord\IOFactory;
use Dcat\Admin\Admin;

class DocsController extends AdminController
{
    // function _construct() { $this->wordCount = 0;} 
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
	// Index List 
    protected function grid()
    {
        return Grid::make(new Doc(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('doc_name')->sortable();
            $grid->column('url')->display(function ($url) {
                $content = '';
                $fileId = substr($url,31,33);
                $content .= '<a href="https://docs.google.com/document/d/'.$fileId.'/edit" target="_blank">https://docs.google.com/document/d/'.$fileId.'/edit</a><br>';
                return $content;
            });
			$grid->column('word_count');
            $grid->column('doc_type')->display('.docx');
            
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
            $grid->showQuickEditButton();
            $grid->disableEditButton();
            $grid->enableDialogCreate();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }
	
    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Doc(), function (Show $show) {
            $show->field('id');
			$show->field('order_id');
			$show->field('translator_id');
            $show->field('doc_name');
            $show->field('word_count');
			$show->field('doc_type');
			$show->field('doc_link');
			$show->field('status');
			$show->field('doc_size');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        Admin::script(
            <<<JS
            $(document).ready(function(){
                $('.file-input').change(function(){
                    var file = $('.file-input').val();
                    console.log(file);
                    if(file.includes('media')){
                        var count = file.split("media");
                        console.log(parseInt(count[1]));
                        $('.field_word_count').val(parseInt(count[1]));
                    }
                    
                    console.log($('.file-input').val());
                });
            });
        JS
        );
        return Form::make(new Doc(), function (Form $form) {
            $wordsCount = 0;
            $form->display('id');
            $form->multipleFile('doc_name')->rules('mimes:doc,docx,txt')->autoUpload()
            ->saving(function ($paths) {
                $wordCount = explode('media',$paths[0]);
                $paths[0] = $wordCount[0]."media";
                return json_encode($paths);
            });

			$form->textarea('notes');
            $form->text('word_count');
            $form->number('doc_price');
            $form->display('created_at');
            $form->display('updated_at');
            
            $form->saving(function (Form $form) use ($wordsCount){
                // dd($wordsCount);
                $form->multipleFile('doc_name')->disk('google')
                ->saving(function ($paths) {
                    return json_encode($paths);
                });
            });
        });
    }
	
	public function docsPushToGoogleDrive(Request $request) {
		dd($request->file("thing")->store(env('GOOGLE_DRIVE_FOLDER_ID'), "google"));
	}

    
     
	
	public function test()
	{
		dd($this->form()->store());
	}
	
}
