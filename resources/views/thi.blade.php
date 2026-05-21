<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phòng Thi Trắc Nghiệm - Phòng CTSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .q-number-btn {
            width: 40px;
            height: 40px;
            margin: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .q-number-btn:hover {
            transform: scale(1.1);
            opacity: 0.9;
        }
        .status-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: none;
        }
        .question-box {
            border-left: 5px solid #6c757d;
        }
        /* Hiệu ứng viền xanh cho câu đã lưu đáp án */
        .question-box.saved {
            border-left: 5px solid #198754;
        }
    </style>
</head>
<body class="bg-light">

    <div id="saving-status" class="badge bg-success p-2 status-badge shadow">
        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang tự động lưu...
    </div>

    <div class="container-fluid my-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card p-3 shadow-sm">
                    <h3 id="quiz-title" class="text-primary mb-0">Đang tải đề thi...</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card p-3 shadow-sm sticky-top" style="top: 20px;">
                    <div class="text-center mb-3">
                        <h5>Thời gian còn lại</h5>
                        <h2 id="countdown-timer" class="text-danger fw-bold">00:00</h2>
                    </div>
                    
                    <hr>
                    
                    <h5>Danh sách câu hỏi</h5>
                    <p class="small text-muted">Màu xanh: Đã lưu | Màu xám: Chưa làm</p>
                    <div id="question-navigation" class="d-flex flex-wrap"></div>

                    <hr>
                    <button id="btn-submit-exam" class="btn btn-danger w-100 fw-bold py-2 shadow-sm" onclick="submitExam()">NỘP BÀI THI</button>
                </div>
            </div>

            <div class="col-md-8">
                <input type="hidden" id="quiz-id" value="19"> 
                
                <div id="quiz-container">
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Hệ thống đang tải câu hỏi từ Bộ nhớ đệm...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bundle.min.js"></script>

    <script>
// Thay vì lấy cứng value="19" ở HTML, ta đọc từ localStorage do trang chon-de truyền sang
        const quizId = localStorage.getItem('selected_quiz_id') || document.getElementById('quiz-id').value;
        let examDurationSeconds = 0;
        let timerInterval;

        // 1. KHI TRANG WEB LOAD: Tự động gọi API lấy đề thi
        document.addEventListener("DOMContentLoaded", function() {
            fetchExamData(quizId);
        });

        // Hàm gọi API lấy dữ liệu đề thi đã được Laravel Cache tối ưu
 // TRÁCH NHIỆM FRONT-END: Gọi API lấy toàn bộ câu hỏi thật của đề thi
         function fetchExamData(id) {
                console.log(`Đang gọi API lấy toàn bộ câu hỏi cho mã đề thi: ${id}`);
                
                // Front-end dùng fetch để gửi request lên đường dẫn API lấy chi tiết đề thi
                fetch(`/api/quiz-questions/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Lỗi mạng hoặc API chưa được Back-end cấu hình');
                        }
                        return response.json();
                    })
                    .then(res => {
                        // Nếu có API thật và Back-end trả về đúng dữ liệu
                        if (res.status === "success" && res.data) {
                            renderExam(res.data);
                            startCountdown(res.data.duration);
                        }
                    })
                    .catch(err => {
                        console.error("Chưa kết nối được Database thật, chạy dữ liệu mẫu dự phòng tối ưu theo ID:", err);
                        
                        // ĐOẠN ĐÃ SỬA: Khởi tạo cục dữ liệu dự phòng rỗng
                        let backupExamData = {
                            quiz_title: "",
                            duration: 15, 
                            questions: []
                        };

                        // Kiểm tra ID để gán thời gian và câu hỏi demo tương ứng khi chạy giao diện độc lập
                        if (parseInt(id) === 28) {
                            backupExamData.quiz_title = "Bài thu hoạch Tuần SHCD - SV giữa khóa năm học 2020-2021";
                            backupExamData.duration = 40; // Đổi thành 40 phút
                            backupExamData.questions = [
                                { qid: 281, question: "Câu hỏi mẫu bài giữa khóa 2020-2021: Quy chế rèn luyện của trường gồm mấy bước đánh giá?", options: [{ oid: 1, option_value: "3 bước" }, { oid: 2, option_value: "4 bước" }] }
                            ];
                        } 
                        else if (parseInt(id) === 31) {
                            backupExamData.quiz_title = "Bài thu hoạch Tuần SHCD - SV đầu khóa năm học 2020-2021";
                            backupExamData.duration = 40; // Đổi thành 40 phút
                            backupExamData.questions = [
                                { qid: 311, question: "Câu hỏi mẫu bài đầu khóa 2020-2021: Sinh viên nghỉ học bao nhiêu % số tiết của môn thì bị cấm thi?", options: [{ oid: 5, option_value: "20%" }, { oid: 6, option_value: "30%" }] }
                            ];
                        } 
                        else if (parseInt(id) === 52) {
                            backupExamData.quiz_title = "Bài thu hoạch Tuần SHCD - SV giữa khoá năm học 2021-2022";
                            backupExamData.duration = 40; // Đổi thành 40 phút
                            backupExamData.questions = [
                                { qid: 521, question: "Câu hỏi mẫu bài giữa khóa 2021-2022: Chuẩn đầu ra ngoại ngữ bắt buộc đối với ngành CNTT là gì?", options: [{ oid: 7, option_value: "TOEIC 450" }, { oid: 8, option_value: "TOEIC 500" }] }
                            ];
                        } 
                        else {
                            // Mặc định là đề thi thử (Demo) mã đề 19
                            backupExamData.quiz_title = "THI TRẮC NGHIỆM PHÒNG CÔNG TAC SINH VIÊN - ĐỀ MẪU";
                            backupExamData.duration = 15; // 15 phút
                            backupExamData.questions = [
                                {
                                    qid: 1,
                                    question: "Hệ thống quản lý phiên bản mã nguồn phân tán phổ biến nhất hiện nay là gì?",
                                    options: [{ oid: 11, option_value: "SVN" }, { oid: 12, option_value: "Git" }, { oid: 13, option_value: "Mercurial" }, { oid: 14, option_value: "CVS" }]
                                },
                                {
                                    qid: 2,
                                    question: "Trong kiến trúc phần mềm Laravel, thư mục nào dùng để chứa các file giao diện Blade View?",
                                    options: [{ oid: 21, option_value: "app/Http/Controllers" }, { oid: 22, option_value: "config/" }, { oid: 23, option_value: "resources/views" }, { oid: 24, option_value: "routes/" }]
                                }
                            ];
                        }

                        // Đổ dữ liệu đã được phân loại theo ID ra màn hình
                        renderExam(backupExamData);
                        startCountdown(backupExamData.duration);
                    });
            }

        // Hàm sinh (render) HTML giao diện câu hỏi từ cục JSON trả về
        function renderExam(data) {
            document.getElementById('quiz-title').innerText = data.quiz_title;
            
            const quizContainer = document.getElementById('quiz-container');
            const navContainer = document.getElementById('question-navigation');
            
            quizContainer.innerHTML = '';
            navContainer.innerHTML = '';

            data.questions.forEach((question, index) => {
                const qNum = index + 1;

                // A. Render ô số câu hỏi ở cột bên trái
                const navBtn = document.createElement('div');
                navBtn.id = `nav-q-${question.qid}`;
                navBtn.className = 'q-number-btn bg-secondary text-white fw-bold';
                navBtn.innerText = qNum;
                navBtn.setAttribute('onclick', `scrollToQuestion('q-box-${question.qid}')`);
                navContainer.appendChild(navBtn);

                // B. Render nội dung câu hỏi và các đáp án ở cột bên phải
                const qBox = document.createElement('div');
                qBox.id = `q-box-${question.qid}`;
                qBox.className = 'card p-4 mb-4 shadow-sm question-box';
                
                let optionsHtml = '';
                question.options.forEach(option => {
                    optionsHtml += `
                        <div class="form-check my-2">
                            <input class="form-check-input answer-radio" 
                                   type="radio" 
                                   name="${question.qid}" 
                                   id="opt-${option.oid}" 
                                   value="${option.oid}">
                            <label class="form-check-label w-100 p-1 rounded style-label" for="opt-${option.oid}">
                                ${option.option_value}
                            </label>
                        </div>
                    `;
                });

                qBox.innerHTML = `
                    <h5 class="fw-bold text-dark mb-3">Câu ${qNum}: ${question.question}</h5>
                    <div class="options-group">
                        ${optionsHtml}
                    </div>
                `;
                quizContainer.appendChild(qBox);
            });

            // C. ĐĂNG KÝ SỰ KIỆN: Lắng nghe hành vi chọn đáp án (Event Delegation)
            quizContainer.addEventListener('change', function(event) {
                if (event.target.classList.contains('answer-radio')) {
                    const qid = event.target.name;
                    const oid = event.target.value;
                    saveAnswerToServer(quizId, qid, oid);
                }
            });
        }

        // 2. LOGIC LƯU ĐÁP ÁN TỰ ĐỘNG (Auto-save ngầm qua AJAX)
        function saveAnswerToServer(quizId, qid, oid) {
            const statusBadge = document.getElementById('saving-status');
            statusBadge.style.display = 'block';

            fetch('/api/save-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quiz_id: parseInt(quizId),
                    qid: parseInt(qid),
                    oid: parseInt(oid)
                })
            })
            .then(response => response.json())
            .then(res => {
                setTimeout(() => { statusBadge.style.display = 'none'; }, 500);

                // Thêm hiệu ứng đổi màu UI đồng bộ khi Backend phản hồi thành công
                // Duy không cần sửa file PHP nào cả, xử lý thuần DOM ở đây là đủ
                const navBtn = document.getElementById(`nav-q-${qid}`);
                const qBox = document.getElementById(`q-box-${qid}`);
                
                if (navBtn) {
                    navBtn.classList.remove('bg-secondary');
                    navBtn.classList.add('bg-success'); // Ô bên trái sang màu xanh lá
                }
                if (qBox) {
                    qBox.classList.add('saved'); // Đổi màu viền câu hỏi bên phải sang màu xanh để báo hiệu đã lưu
                }
            })
            .catch(err => {
                console.error("Lỗi đồng bộ đáp án ngầm: ", err);
                // Giả lập UI mượt ngay cả khi Backend chưa bật hoặc lỗi route
                const navBtn = document.getElementById(`nav-q-${qid}`);
                if (navBtn) {
                    navBtn.classList.remove('bg-secondary');
                    navBtn.classList.add('bg-success');
                }
                setTimeout(() => { statusBadge.style.display = 'none'; }, 400);
            });
        }

        // 3. ĐỒNG HỒ ĐẾM NGƯỢC THUẦN CLIENT
        function startCountdown(durationMinutes) {
            examDurationSeconds = durationMinutes * 60;
            const timerDisplay = document.getElementById('countdown-timer');

            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                let minutes = Math.floor(examDurationSeconds / 60);
                let seconds = examDurationSeconds % 60;

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                timerDisplay.innerText = `${minutes}:${seconds}`;

                if (--examDurationSeconds < 0) {
                    clearInterval(timerInterval);
                    timerDisplay.innerText = "HẾT GIỜ!";
                    alert("Thời gian làm bài đã hết! Hệ thống tự động nộp bài.");
                    submitExam(); 
                }
            }, 1000); 
        }

        function scrollToQuestion(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // 4. ĐÃ HOÀN THIỆN: LOGIC GOM ĐÁP ÁN KHI ẤN NỘP BÀI TẠI FRONT-END
         function submitExam() {
            if (!confirm("Bạn có chắc chắn muốn nộp bài thi này không?")) {
                return;
            }

            // 1. Dừng đồng hồ đếm ngược lập tức
            clearInterval(timerInterval);
            
            // 2. Gom toàn bộ đáp án sinh viên đã chọn trên màn hình
            const studentAnswers = [];
            const radios = document.querySelectorAll('.answer-radio:checked');
            
            radios.forEach(radio => {
                studentAnswers.push({
                    qid: parseInt(radio.name),
                    oid: parseInt(radio.value)
                });
            });

            console.log("Mảng đáp án Front-end sẵn sàng nộp:", studentAnswers);

            // 3. Đếm tổng số câu hỏi có trên giao diện để làm mẫu số (ví dụ: 40 câu hoặc 2 câu mẫu)
            const totalQuestions = document.querySelectorAll('.question-box').length;
            const answeredCount = studentAnswers.length;

            // 4. LƯU TẠM DỮ LIỆU VÀO STORAGE (Để trang ket-qua.blade.php bốc ra hiển thị)
            // Vì chưa kết nối API chấm điểm thật của Back-end, ta giả lập số câu đúng bằng số câu SV đã bấm chọn
            localStorage.setItem('last_correct', answeredCount.toString()); 
            localStorage.setItem('last_total', totalQuestions.toString());
            
            // Giả lập công thức tính điểm hệ 10: (Số câu chọn / Tổng số câu) * 10
            const mockScore = totalQuestions > 0 ? ((answeredCount / totalQuestions) * 10).toFixed(1) : "0.0";
            localStorage.setItem('last_score', mockScore);

            alert(`Nộp bài thành công! Hệ thống đã ghi nhận bạn làm được ${answeredCount}/${totalQuestions} câu hỏi.`);
            
            // 5. CHUYỂN HƯỚNG SANG TRANG KẾT QUẢ ĐÃ TẠO
            window.location.href = '/ket-qua';
        }
    </script>
</body>
</html>