<?php

namespace App\Admin\Controllers;

use App\model\user;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
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
        $grid = new Grid(new user);

        $grid->id('Id');
        $grid->nickname('名称');
        $grid->sex('性别');
        $grid->province('所在省');
        $grid->country('国家');
        $grid->city('所在市');
        $grid->headimgurl('头像')->display(function($img){
           return  '<img src="'.$img.'">';
        });

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
        $show = new Show(user::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->city('City');
        $show->province('Province');
        $show->country('Country');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        $show->sub_status('Sub status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new user);

        $form->text('openid', 'Openid');
        $form->text('nickname', 'Nickname');
        $form->text('sex', 'Sex');
        $form->text('city', 'City');
        $form->text('province', 'Province');
        $form->text('country', 'Country');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');
        $form->switch('sub_status', 'Sub status')->default(1);

        return $form;
    }
}
