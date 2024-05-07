<?php

namespace crmeb\form\components;

use crmeb\form\BaseComponent;
use crmeb\form\BuildInterface;

class Arr extends BaseComponent implements BuildInterface
{
    //组件名
    const NAME = 'arr';

    /**
     * 规则
     * @var array[]
     */
    protected $rule_ = [
        'title' => '提现面额',
        'value' => [],
        'type' => 'group',
        'field' => 'user_extract_price_values',
        'info' => '以gaga币为单位，单位：个，提现会扣除手续费再到账',
        // 'disabled' => false,
        // 'vertical' => false,
        'placeholder' => '请输入',
        'props' => [
            'rule' => ['type' => 'input', 'field' => 'user_extract_price_values[]', 'title' => '', 'placeholder' => '请输入'],
            'field' => 'user_extract_price_values[]'
        ],
        // 'prefix' => '',
        // 'rows' => 2,
        // 'copy' => false,
        // 'copyText' => '',
        // 'randToken' => 0,
        // 'maxlength' => null,
        // 'options' => []
    ];

    /**
     * 组件规则
     * @var array
     */
    protected $rule = [
        'type' => 'group',
        'title' => '',
        'field' => '',
        'value' => [],
        'info' => '',
        'vertical' => false,
        'control' => [],
        'options' => [],
        'props' => [],
    ];


    public function __construct(string $field, string $title, $value = null)
    {
        $this->rule['title'] = $title;
        $this->rule['field'] = $field;
        $this->rule['value'] = !is_null($value) ? $value : [];
    }

    /**
     * 提示语
     * @param string $info
     * @return $this
     */
    public function info(string $info)
    {
        $this->rule['info'] = $info;
        return $this;
    }

    public function options(array $options = [])
    {
        $this->rule['options'] = $options;
        return $this;
    }

    public function props(array $props = [])
    {
        $this->rule['props'] = $props;
        return $this;
    }

    public function vertical(bool $vertical)
    {
        $this->rule['vertical'] = $vertical;
        return $this;
    }

    public function toArray(): array
    {
        $this->rule['name'] = self::NAME;
        $control = [];
        foreach ($this->rule['control'] as $item) {
            $data = ['value' => $item['value'], 'componentsModel' => []];
            foreach ($item['componentsModel'] as $value) {
                if ($value instanceof BuildInterface) {
                    $data['componentsModel'][] = $value->toArray();
                }
            }
            $control[] = $data;
        }
        $this->rule['control'] = $control;
        $this->before();
        return $this->rule;
    }
}