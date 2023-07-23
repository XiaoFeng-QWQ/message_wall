function dragElement(elmnt) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    
    // 添加鼠标事件
    elmnt.onmousedown = dragMouseDown;

    // 添加触摸事件
    elmnt.ontouchstart = dragMouseDown;

    function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        
        // 使用鼠标事件和触摸事件
        var clientX, clientY;
        if (e.type === 'touchstart') {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
        } else {
        clientX = e.clientX;
        clientY = e.clientY;
        }
        
        // 在启动时获取光标/触摸点位置:
        pos3 = clientX;
        pos4 = clientY;

        // 添加鼠标事件
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;

        // 添加触摸事件
        document.ontouchend = closeDragElement;
        document.ontouchmove = elementDrag;
    }

    function elementDrag(e) {
        e = e || window.event;
        // e.preventDefault();
        
        // 使用鼠标事件和触摸事件
        var clientX, clientY;
        if (e.type === 'touchmove') {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }
        
        // 计算新的光标/触摸点位置:
        pos1 = pos3 - clientX;
        pos2 = pos4 - clientY;
        pos3 = clientX;
        pos4 = clientY;

        // 设置元素的新位置:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        // 释放鼠标按钮/触摸点时停止移动:
        document.onmouseup = null;
        document.onmousemove = null;

        document.ontouchend = null;
        document.ontouchmove = null;
    }
}