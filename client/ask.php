
<style>
.ask-container {
    min-height: 85vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 35px 15px;
    background: linear-gradient(135deg, #e0e7ff, #f5f3ff);
}

.ask-card {
    width: 100%;
    max-width: 650px;
    padding: 35px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 10px 35px rgba(44, 62, 120, 0.15);
}

.ask-heading {
    margin: 0 0 10px;
    text-align: center;
    color: #4338ca;
    font-size: 30px;
    font-weight: 700;
}

.ask-subtitle {
    margin-bottom: 30px;
    text-align: center;
    color: #777777;
    font-size: 14px;
}

.ask-field {
    margin-bottom: 22px;
}

.ask-field label {
    display: block;
    margin-bottom: 9px;
    color: #333333;
    font-weight: 600;
}

.ask-field input,
.ask-field select,
.ask-field textarea {
    display: block;
    width: 100%;
    padding: 13px 14px;
    border: 1px solid #d5d9e5;
    border-radius: 9px;
    background: #fafbff;
    color: #333333;
    font-family: Arial, sans-serif;
    font-size: 15px;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.ask-field textarea {
    resize: vertical;
    min-height: 140px;
}

.ask-field input:focus,
.ask-field select:focus,
.ask-field textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    background: #ffffff;
}

.ask-button {
    display: block;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 9px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.ask-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
}

.ask-footer {
    margin-top: 22px;
    text-align: center;
    color: #777777;
    font-size: 13px;
}

@media (max-width: 576px) {
    .ask-card {
        padding: 25px 20px;
    }

    .ask-heading {
        font-size: 26px;
    }
}
</style>
<div class="ask-container">
    <div class="ask-card">

        <h1 class="ask-heading">Ask a Question</h1>

        <p class="ask-subtitle">
            Have a doubt? Share your question with us.
        </p>

        <form class="ask-form"
      action="/project1/server/request.php"
      method="POST"
      onsubmit="alert('Form is submitting!')">

    <input type="hidden" name="ask_question" value="1">
            <div class="ask-field">
                <label for="question-title">Question Title</label>
                <input
                    type="text"
                    name="question_title"
                    id="question-title"
                    placeholder="Enter your question title"
                    required>
            </div>

            <div class="ask-field">
                <label for="question-category">Category</label>
                <?php
                
       include __DIR__ . '/category-dropdown.php';

                ?>
            </div>

            <div class="ask-field">
                <label for="question-description">
                    Describe Your Question
                </label>
                <textarea
                    name="question_description"
                    id="question-description"
                    rows="6"
                    placeholder="Explain your question in detail..."
                    required></textarea>
            </div>

            <button type="submit" name="ask_question" class="ask-button">
    Submit Question
</button>

        </form>

        <div class="ask-footer">
            Please provide clear details so others can help you.
        </div>

    </div>
</div>
