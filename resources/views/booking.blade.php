<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">

    <title>ĐẶT PHÒNG</title>
    <style>
        #alert{
            transition: all 0.2s linear;
        }
    </style>
</head>
<body>

<div class="container mt-5 border pt-4">
{{--    <input type="text" placeholder="Họ và tên" name="full_name">--}}
{{--    <input type="text" placeholder="Họ và tên" name="full_name">--}}
{{--    <input type="text" placeholder="Họ và tên" name="full_name">--}}
{{--    <input type="text" placeholder="Họ và tên" name="full_name">--}}
{{--    <input type="date" name="checkin">--}}
{{--    <input type="date" name="checkout">--}}
    <div class="alert  alert-dismissible" id="alert" role="alert">
        <span id="message"></span>

    </div>
    <form class="row p-4" >
        @csrf
        <div class="form-group col-md-4">
            <label for="full_name">Họ và tên</label>
            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Họ và tên">
        </div>
        <div class="form-group col-md-4">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" placeholder="abc@gmail.com">
        </div>
        <div class="form-group col-md-4">
            <label for="phone">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" >
        </div>
        <div class="form-group col-md-4">
            <label for="room_type">Loại phòng</label>
            <select class="form-control" id="room_type" name="room_type" >
                <option selected>Chọn loại phòng</option>

            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="room_code">Danh sách phòng</label>
            <select class="form-control" id="room_code" name="room_code">

            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="quantity">Số lượng người</label>
            <input type="number" class="form-control" id="quantity" name="quantity">
        </div>
        <div class="form-group col-md-4">
            <label for="checkin">Ngày đến</label>
            <input type="date" class="form-control" id="checkin" name="checkin">
        </div>
        <div class="form-group col-md-4">
            <label for="checkout">Ngày đi</label>
            <input type="date" class="form-control" id="checkout" name="checkout">
        </div>
        <div class="form-group col-md-4">
            <label for="total_price">Tổng chi phí</label>
            <input type="text" disabled class="form-control" id="total_price" name="total_price">
        </div>
        <div class="text-center col-12 d-flex justify-content-end">
            <button type="button" id="order_room" class="btn btn-primary">Đặt phòng</button>
        </div>
    </form>
</div>

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var checkInDate = null;
        var checkOutDate = null;
        var priceEl = $("#total_price");
        var rooms = [];
        const typeDropdown = $("#room_type");
        const roomDropdown = $("#room_code");
        $("#alert").addClass("d-none")

        roomDropdown.change((e) => {
            setPrice();
        })

        typeDropdown.change(setPrice());

        $("#checkin").change(function (event) {
            checkInDate = new Date(event.target.value);
            let result = null;
            let today = new Date();
            if(checkInDate.getTime() < today.getTime()){
                checkInDate = null;
                $("#checkin").val(result);
                alert('Ngày đến không hợp lệ');
            }
            else{
                result = checkInDate.getFullYear() + "-" + ("0" +( checkInDate.getMonth() + 1)).slice(-2) + "-" + ("0" + checkInDate.getDate()).slice(-2) ;
                $("#checkin").val(result);
                setPrice();
            }

        })

        $("#checkout").change(function (event) {
            checkOutDate = new Date(event.target.value);
            let result = null;
            if(checkOutDate.getTime() < checkInDate){
                checkOutDate = null;
                $("#checkout").val(result);
                alert('Ngày trả phòng không hợp lệ');
            }
            else {
                result = checkOutDate.getFullYear() + "-" + ("0" +( checkOutDate.getMonth() + 1)).slice(-2) + "-" + ("0" + checkOutDate.getDate()).slice(-2) ;

                $("#checkout").val(result);
                setPrice();

            }

        })

        function setPrice(){
            if(checkInDate && checkOutDate){
                let room = rooms.find(room => room.id == roomDropdown.val());
                const days = (checkOutDate - checkInDate) / (1000 * 3600 * 24);
                priceEl.val(formatMoney(days * room.price));
            }
            return null;
        }

        function formatMoney(value){
            return value.toLocaleString('it-IT', {style: 'currency', currency: 'VND'}) + "";
        }

        $.ajax({
            url: 'http://localhost:8000/api/room-type',
            type: 'GET',
            dataType: 'json',
            success: function (data) {

                for(let d of data){
                    let option = `<option value=${d.id}>${d.type}</option>`
                    typeDropdown.append(option)
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('Error in Operation');
            }
        });
            // button.click(() => {
            //
            //     ///
            // })
        $.ajax({
            url: 'http://localhost:8000/api/room',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                rooms = data;
                for(let room of rooms){
                    let money = formatMoney(room.price) ;
                    let option = `<option id=${room.id} roomType=${room.room_type_id} value=${room.id}>${room.room_code} - ${money}</option>`
                    roomDropdown.append(option)
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('Error in Operation');
            }
        });

        typeDropdown.change((event) => {
            const typeId = event.target.value;
            let showRoom = rooms.filter(room => room.room_type_id == typeId);
            let roomOptions = $("#room_code option");
            roomOptions.remove()
            for(room of showRoom){
                let money = formatMoney(room.price) ;
                let newOption = `<option id=${room.id} roomType=${room.room_type_id}
                        value=${room.id}>${room.room_code} - ${money}</option>`
                roomDropdown.append(newOption);
            }
        })

        $("#order_room").click(() => {
            var newCheckIn = null;
            var checkInStr = null;
            var newCheckOut = null;
            var checkOutStr = null;
            if(checkInDate){
                 newCheckIn = new Date(checkInDate);
                 checkInStr = newCheckIn.getFullYear() + "-" +
                    ("0" +( newCheckIn.getMonth() + 1)).slice(-2) + "-" + ("0" + newCheckIn.getDate()).slice(-2) ;
            }
            if(checkOutDate){
                newCheckOut = new Date(checkOutDate);
                checkOutStr = newCheckOut.getFullYear() + "-" +
                    ("0" +( newCheckOut.getMonth() + 1)).slice(-2) + "-" + ("0" + newCheckOut.getDate()).slice(-2) ;
            }
            if(checkInStr && checkOutStr){
                let data = {};
                data["full_name"] = $("#full_name").val();
                data["email"] = $("#email").val();
                data["phone"] = $("#phone").val();
                data["quantity"] = Number.parseInt($("#quantity").val());
                data["checkin"] = checkInStr;
                data["checkout"] = checkOutStr;
                data["room_id"] = Number.parseInt(roomDropdown.val());
                console.log(data)

                $.ajax({
                    url: 'http://127.0.0.1:8000/api/booking',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        console.log(response)
                        $("#message").text(response.message);
                        const alert = $("#alert");
                        alert.removeClass("d-none");
                        alert.addClass("d-block");
                        let nameStatus = "";
                        if(response.type === "Success"){
                            nameStatus = "alert-success"
                        }else{
                            nameStatus = "alert-danger"
                        }
                        alert.addClass(nameStatus)
                        setTimeout(() => {
                            alert.removeClass("d-block");
                            alert.addClass("d-none");
                            alert.removeClass(nameStatus)
                            nameStatus = ""
                        }, 3500);

                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.log('Fail to check');
                    }
                })
            }else{
                console.log(roomDropdown.val())
            }
        })
    });
</script>
</body>
</html>
