<?php

namespace App\Admin\Controllers;

use App\model\order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
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
        $grid = new Grid(new order);

        $grid->o_id('O id');
        $grid->order_no('Order no');
        $grid->order_amount('Order amount');
        $grid->create_time('Create time');
        $grid->uid('Uid');
        $grid->pay_time('Pay time');
        $grid->pay_amount('Pay amount');
        $grid->is_del('Is del');

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
        $show = new Show(order::findOrFail($id));

        $show->o_id('O id');
        $show->order_no('Order no');
        $show->order_amount('Order amount');
        $show->create_time('Create time');
        $show->uid('Uid');
        $show->pay_time('Pay time');
        $show->pay_amount('Pay amount');
        $show->is_del('Is del');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new order);

        $form->number('o_id', 'O id');
        $form->text('order_no', 'Order no');
        $form->text('order_amount', 'Order amount');
        $form->number('create_time', 'Create time');
        $form->number('uid', 'Uid');
        $form->number('pay_time', 'Pay time');
        $form->number('pay_amount', 'Pay amount');
        $form->number('is_del', 'Is del');

        return $form;
    }
}
