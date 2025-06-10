<center>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; font-family: 'Arial', sans-serif; color: #111827; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <tr>
            <td style="padding: 30px 20px; text-align: center; background: linear-gradient(135deg, #2563eb, #7c3aed); border-radius: 8px 8px 0 0;">
                <h1 style="color: #ffffff; font-size: 24px; margin: 0; font-weight: 600;">Account Activation</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 20px; text-align: left;">
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Hello {{ $username }},</p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Thank you for registering with {{ $webName }}! To complete your registration and activate your account, please click the button below.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $url }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #2563eb, #7c3aed); color: #ffffff; text-decoration: none; font-weight: 600; border-radius: 9999px; font-size: 16px;">Activate Account</a>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">This activation link will expire in 24 hours. If you didn't create an account, you can safely ignore this email.</p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">Alternatively, you can copy and paste the following link into your browser:</p>
                <p style="font-size: 14px; line-height: 1.4; color: #4b5563; word-break: break-all; background-color: #f9fafb; padding: 10px; border-radius: 4px; border: 1px solid #e5e7eb;">{{ $url }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                <p style="font-size: 14px; color: #6b7280; margin: 0;">&copy; {{ date('Y') }} {{ $webName }}. All rights reserved.</p>
            </td>
        </tr>
    </table>
</center>