        (function () {
            const trigger = document.querySelector('.mascot-speech-trigger');
            const messageElement = document.querySelector('.speech-bubble__text');
            if (!trigger || !messageElement) {
                return;
            }

            if (!('speechSynthesis' in window) || !('SpeechSynthesisUtterance' in window)) {
                trigger.remove();
                return;
            }

            let selectedVoice = null;
            const selectVoice = () => {
                const voices = window.speechSynthesis.getVoices();
                selectedVoice = voices.find((voice) => voice.lang && voice.lang.toLowerCase().startsWith('es')) ||
                    voices.find((voice) => voice.lang && voice.lang.toLowerCase().startsWith('en')) ||
                    voices[0] || null;
            };

            selectVoice();
            window.speechSynthesis.addEventListener('voiceschanged', selectVoice);

            const speakMessage = () => {
                const text = messageElement.textContent.trim();
                if (!text) {
                    return;
                }

                const synth = window.speechSynthesis;
                synth.cancel();

                const utterance = new SpeechSynthesisUtterance(text);
                if (selectedVoice) {
                    utterance.voice = selectedVoice;
                }
                utterance.rate = 1;
                utterance.pitch = 1;

                synth.speak(utterance);
            };

            trigger.addEventListener('click', speakMessage);
            trigger.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    speakMessage();
                }
            });
        }());