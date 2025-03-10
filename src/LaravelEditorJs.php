<?php

namespace AlAminFirdows\LaravelEditorJs;

use EditorJS\EditorJS;
use EditorJS\EditorJSException;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class LaravelEditorJs
{
    /**
     * Render blocks
     *
     * @param string $data
     * @return string
     * @throws Exception
     */
    public function render(string $data,$template_dir) : string
    {

        try {
            $configJson = json_encode(config('laravel_editorjs.config') ?: []);

            $editor = new EditorJS($data, $configJson);

            $renderedBlocks = [];



            foreach ($editor->getBlocks() as $block) {

                $viewName = "laravel_editorjs::{$template_dir}." . Str::snake($block['type'], '-');

                if (! View::exists($viewName)) {
                    if($template_dir === 'default')
                    {
                        $viewName = "laravel_editorjs::default.not-found";
                    } else
                    {
                        $viewName = "laravel_editorjs::default." . Str::snake($block['type'], '-');
                        if(!View::exists($viewName)){
                            $viewName = "laravel_editorjs::default.not-found";
                        }
                    }
                }

                $renderedBlocks[] = view($viewName, [
                    'type' => $block['type'],
                    'data' => $block['data']
                ])->render();
            }

            return implode($renderedBlocks);
        } catch (EditorJSException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
