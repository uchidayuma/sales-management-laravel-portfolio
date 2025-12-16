let preX = null;//x軸始点
let preY = null;//y軸始点
let moveX = null;
let moveY = null;

let postX = null;//x軸終点
let postY = null;//y軸終点
let mouseEvent = false;
let storedLines = [];
let storedLength = [];

const can = document.getElementById("canvas");
const ctx = can.getContext("2d");

function drowLayout(){
    can.onclick = function(e){
        if(mouseEvent == false){
            preX = e.offsetX;
            preY = e.offsetY;
            mouseEvent = true;
        }else{
            postX = e.offsetX;
            postY = e.offsetY;
            storedLines.push({
                x1: preX,
                y1: preY,
                x2: postX,
                y2: postY
            })
            let length = calcLength(preX, postX, preY, postY);
            storedLength.push({length});
            
            let diffx = Math.abs(postX - storedLines[0]['x1']);
            let diffy = Math.abs(postY - storedLines[0]['y1']);
            if(storedLines.length >= 3){
                if(diffx <= 5 && diffy <= 5){
                    storedLines[storedLines.length - 1]['x2'] = storedLines[0]['x1'];
                    storedLines[storedLines.length - 1]['y2'] = storedLines[0]['y1'];
                    console.log(storedLines);
                    
                    //条件を満たしたときに自動的に閉路を完成させる
                    ctx.beginPath();
                    ctx.clearRect(0,0,can.width,can.height);
                    
                    ctx.moveTo(storedLines[0]['x1'], storedLines[0]['y1']);
                    ctx.lineTo(storedLines[0]['x2'], storedLines[0]['y2']);
                    for(let i = 1; i < storedLines.length; i++){
                        ctx.lineTo(storedLines[i]['x2'], storedLines[i]['y2']);
                    }
                    ctx.lineWidth = 3; 
                    ctx.strokeStyle = "gray";
                    ctx.fillStyle = "#d9d9d9";
                    ctx.fill();
                    ctx.stroke()

                    for(let i = 0; i < storedLength.length; i++){
                        drowLength(storedLines[i].x1, storedLines[i].x2, storedLines[i].y1, storedLines[i].y2);
                    }

                    calcSomeArea();

                    mouseEvent = false;
                    return;
                }
            }
            preX = postX;
            preY = postY;
            console.log(storedLines);
            return can.onclick;
        }
    };

    can.onmousemove = function(e){
        if(mouseEvent == true){
            Redraw();
            moveX = e.offsetX;
            moveY = e.offsetY;
            ctx.strokeStyle = "#000";
            ctx.lineWidth = 3;  
            ctx.lineJoin  = "round";
            ctx.lineCap   = "round";
            ctx.beginPath();
            ctx.moveTo(preX,preY);
            ctx.lineTo(moveX,moveY);
            ctx.stroke();
            /* リアルタイムで長さを表示 */
            drowLength(preX, moveX, preY, moveY);
        }
    };

    function Redraw(){
        ctx.clearRect(0,0,can.width,can.height);
        if(storedLines.length == 0){
            return;
        }
        for(let i = 0; i<storedLines.length; i++){
            ctx.beginPath();
            ctx.moveTo(storedLines[i].x1, storedLines[i].y1);
            ctx.lineTo(storedLines[i].x2, storedLines[i].y2);
            ctx.stroke();

            drowLength(storedLines[i].x1, storedLines[i].x2, storedLines[i].y1, storedLines[i].y2);
        }
    }

    //mm表記で長さを算出
    function calcLength(x1, x2, y1, y2) {
        let widthSquare = (x1 - x2)*(x1 - x2);
        let heigthSquare = (y1 - y2)*(y1 - y2);
        let length = Math.floor(Math.sqrt(widthSquare + heigthSquare)*10);
        return length;
    }

    //長さの表示
    function drowLength(x1, x2, y1, y2){
        let fontX = Math.abs(x1 + x2)/2;
        let fontY = Math.abs(y1 + y2)/2;
        ctx.font = "24px Arial";
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = 'red';
        let length = calcLength(x1, x2, y1, y2);
        ctx.fillText(length, fontX, fontY);  
    }

    can.onmouseout = function(){
        mouseEvent = false;
    };

    //描画エリアのクリア
    const clear_btn = document.getElementById("clear_btn");
    clear_btn.onclick = function(){
        ctx.beginPath();
        ctx.clearRect(0,0,can.width,can.height);
        storedLines.length=0;
        storedLength.length=0;
    };

    function calcSomeArea(){
        let n = storedLines.length;
        let sum = 0;
        let ans = 0;
        for(let i = 0; i < n; i++){
            if(i != n-1){
                sum += (storedLines[i]['x1'] - storedLines[i+1]['x1']) * (storedLines[i]['y1'] + storedLines[i+1]['y1']);
                console.log(sum)
            }else if(i == n-1){
                sum +=  (storedLines[i]['x1'] - storedLines[0]['x1']) * (storedLines[i]['y1'] + storedLines[0]['y1']);
                console.log(sum)
            }
        }
        ans = Math.abs(sum) / 2;
        ans = Math.ceil(ans/1000)/10;
        
        ctx.font = "24px Arial";
        ctx.textAlign = 'end';
        ctx.textBaseline = 'top';
        ctx.fillStyle = 'black';
        ctx.fillText("面積", can.width-10, 5)
        ctx.fillText(ans+"㎡", can.width, 30); 
    }

    const end_btn = document.getElementById("end_btn");
    end_btn.onclick = function(){
        console.log(mouseEvent)
        alert('完了しました'); 
    }

    console.log(mouseEvent)
}

function setTurfSample(val) {
    console.log(val)
    var imgWidth = document.getElementById('img-width').value;
    var imgHeight = document.getElementById('img-height').value;
    var isDragging = false;
    var dragTarget = null; // ドラッグ対象

    var srcs = [
        '../../images/shintou-background.png',
        '../../images/sales/68.jpg'
    ];
    var images = [];
    for (var i in srcs) {
        images[i] = new Image();
        images[i].src = srcs[i];
    }

    //ここで全部の画像を一気においてるっぽい
    //ToDo: valから画像を指定しておけるように
    var loadedCount = 0;
    images[val].addEventListener('load', function() {
        var x = 0;
        var y = 0;
        var w = imgWidth;
        var h = imgHeight;
        images[val].drawOffsetX = x;
        images[val].drawOffsetY = y;
        images[val].drawWidth   = w;
        images[val].drawHeight  = h;

        ctx.drawImage(images[val], x, y, w, h);
        x += 50;
        y += 70;
    }, false);

    // ドラッグ開始
    var mouseDown = function(e) {
        var posX = parseInt(e.clientX - can.offsetLeft);
        var posY = parseInt(e.clientY - can.offsetTop);

        for (var i = images.length - 1; i >= 0; i--) {
            // 当たり判定（ドラッグした位置が画像の範囲内に収まっているか）
            if (posX >= images[i].drawOffsetX &&
                posX <= (images[i].drawOffsetX + images[i].drawWidth) &&
                posY >= images[i].drawOffsetY &&
                posY <= (images[i].drawOffsetY + images[i].drawHeight)
            ) {
              dragTarget = i;
              isDragging = true;
              break;
            }
        }
    };

    var mouseUp = function(e) {
        isDragging = false;
    };

    var mouseOut = function(e) {
        // canvas外にマウスカーソルが移動した場合ドラッグ終了
        mouseUp(e);
    }

    var mouseMove = function(e) {
        // ドラッグ終了位置
        var posX = parseInt(e.clientX - can.offsetLeft);
        var posY = parseInt(e.clientY - can.offsetTop);

        if (isDragging) {
            // canvas内を一旦クリア
            //ドラッグしようとすると選択してないほうが消えてしまう
            ctx.clearRect(0, 0, can.width, can.height);

            var x = 0;
            var y = 0;
            var w = imgWidth;
            var h = imgHeight-50;
            for (var i in images) {
                if (i == dragTarget) {
                    x = posX - images[i].drawWidth / 2;
                    y = posY - images[i].drawHeight / 2;

                    images[i].drawOffsetX = x;
                    images[i].drawOffsetY = y;
                } else {
                    x = images[i].drawOffsetX;
                    y = images[i].drawOffsetY;
                }
                w = images[i].drawWidth;
                h = images[i].drawHeight;

                ctx.drawImage(images[i], x, y, w, h);
            }
        }
    };

    can.addEventListener('mousedown', function(e){mouseDown(e);}, false);
    can.addEventListener('mousemove', function(e){mouseMove(e);}, false);
    can.addEventListener('mouseup',   function(e){mouseUp(e);},   false);
    can.addEventListener('mouseout',  function(e){mouseOut(e);},  false);
};