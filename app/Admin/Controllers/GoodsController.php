<?php

namespace App\Admin\Controllers;

use App\model\goods;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new goods);

        $grid->g_id('G id');
        $grid->goods_name('商品名称');
        $grid->goods_price('商品价格');
        $grid->goods_num('库存');
        $grid->is_show('是否上架');
        $grid->file_path('图片')->image();
        

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(goods::findOrFail($id));

        $show->g_id('G id');
        $show->goods_name('Goods name');
        $show->goods_price('Goods price');
        $show->goods_num('Goods num');
        $show->is_show('Is show');
        $show->buy_number('Buy number');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new goods);

        $form->number('g_id', 'G id');
        $form->text('goods_name', 'Goods name');
        $form->decimal('goods_price', 'Goods price');
        $form->number('goods_num', 'Goods num')->default(100);
        $form->number('is_show', 'Is show')->default(1);
        // $form->number('buy_number', 'Buy number')->default(1);
        $form-> file('file_path','File path');

        return $form;
    }
}
