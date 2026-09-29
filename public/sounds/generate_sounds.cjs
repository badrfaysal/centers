const fs = require('fs');

function generateWav(filename, frequency1, frequency2, durationSecs, decayFactor) {
    const sampleRate = 44100;
    const numSamples = sampleRate * durationSecs;
    const buffer = Buffer.alloc(44 + numSamples * 2);

    // RIFF header
    buffer.write('RIFF', 0);
    buffer.writeUInt32LE(36 + numSamples * 2, 4);
    buffer.write('WAVE', 8);
    // fmt chunk
    buffer.write('fmt ', 12);
    buffer.writeUInt32LE(16, 16); // chunk size
    buffer.writeUInt16LE(1, 20); // PCM
    buffer.writeUInt16LE(1, 22); // mono
    buffer.writeUInt32LE(sampleRate, 24); // sample rate
    buffer.writeUInt32LE(sampleRate * 2, 28); // byte rate
    buffer.writeUInt16LE(2, 32); // block align
    buffer.writeUInt16LE(16, 34); // bits per sample
    // data chunk
    buffer.write('data', 36);
    buffer.writeUInt32LE(numSamples * 2, 40);

    for (let i = 0; i < numSamples; i++) {
        const t = i / sampleRate;
        
        let sample = 0;
        if (frequency2) {
             // two notes (like a ding-dong)
             if (t < durationSecs / 2) {
                 const env = Math.exp(-decayFactor * t);
                 sample = Math.sin(2 * Math.PI * frequency1 * t) * env;
             } else {
                 const t2 = t - durationSecs/2;
                 const env = Math.exp(-decayFactor * t2);
                 sample = Math.sin(2 * Math.PI * frequency2 * t2) * env;
             }
        } else {
             const env = Math.exp(-decayFactor * t);
             sample = Math.sin(2 * Math.PI * frequency1 * t) * env;
        }

        // Add some harmonics for a richer sound (bell-like)
        let harmonic1 = 0;
        let harmonic2 = 0;
        if (t < durationSecs / 2) {
            harmonic1 = Math.sin(2 * Math.PI * frequency1 * 2 * t) * Math.exp(-decayFactor * 2 * t) * 0.3;
            harmonic2 = Math.sin(2 * Math.PI * frequency1 * 3 * t) * Math.exp(-decayFactor * 3 * t) * 0.15;
        } else {
            const t2 = t - durationSecs/2;
            harmonic1 = Math.sin(2 * Math.PI * frequency2 * 2 * t2) * Math.exp(-decayFactor * 2 * t2) * 0.3;
            harmonic2 = Math.sin(2 * Math.PI * frequency2 * 3 * t2) * Math.exp(-decayFactor * 3 * t2) * 0.15;
        }
        
        sample = sample + harmonic1 + harmonic2;

        const maxAmpl = 32767 * 0.7; // 70% volume to prevent clipping
        buffer.writeInt16LE(Math.max(-32768, Math.min(32767, Math.round(sample * maxAmpl))), 44 + i * 2);
    }
    
    fs.writeFileSync(filename, buffer);
}

// "Ding-dong" (Airport/Hospital style chime) for notifications
generateWav('d:\\Projects\\Centers\\Centers\\public\\sounds\\chime.wav', 659.25, 523.25, 2.0, 3.5);

// "I'm ready" (positive quick beep/chime: low to high)
generateWav('d:\\Projects\\Centers\\Centers\\public\\sounds\\ready.wav', 523.25, 659.25, 1.2, 5.0);

console.log('Sounds generated.');
