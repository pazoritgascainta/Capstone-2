function showFAQ(faqId) {
    const faqs = document.querySelectorAll('.faq');
    faqs.forEach(faq => {
      faq.style.display = 'none';
    });
  
    const selectedFAQ = document.getElementById(faqId);
    if (selectedFAQ) {
      selectedFAQ.style.display = 'block';
    }
  }
  