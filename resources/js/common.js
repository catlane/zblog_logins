$(function () {
    $('.readme code.copy').on('click', function() {
        var str = $.trim($(this).html())
        str = str.replace(/&amp;/g, '&')
        clipboard.writeText(str)
        layer.msg("已复制到剪贴板")
    })

    $('.options input[type="checkbox"]').on('click', function() {
        var $item = $(this).parents('.readme-item')
        if (this.checked) {
            $item.find('.preview a').attr('target', '_blank')
        } else {
            $item.find('.preview a').removeAttr('target')
        }
        $item.find('.code-pre').val($item.find('.preview').html())
    })

    $('.options .copy-btn').on('click', function() {
        var str = $(this).parents('.readme-item').find('.code-pre').val()
        str = $.trim(str)
        clipboard.writeText(str)
        layer.msg("已复制到剪贴板")
    })
})